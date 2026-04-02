# RBAC Architecture Review & Improvements

> Reviewed through two lenses:
> **WWTD** — What Would Taylor Otwell Do? (Laravel idioms, conventions, simplicity)
> **General Architecture** — SOLID, DDD, Clean Architecture, Defense in Depth

---

## Part 1 — Laravel-Specific Issues (WWTD Lens)

### 1. `TicketTypePolicy::viewAny()` checks roles instead of permissions

Every other policy checks permissions via `hasPermission()` or `hasAnyPermission()`. `TicketTypePolicy::viewAny()` breaks this by checking role slugs directly — two authorization models in the same codebase.

```php
// Before — role-based, inconsistent
public function viewAny(User $user): bool
{
    return $user->hasAnyRole([
        PreservedRoleList::SuperAdmin->value,
        PreservedRoleList::Admin->value,
        PreservedRoleList::OrganizationAdmin->value,
    ]);
}

// After — permission-based, consistent with all other policies
public function viewAny(User $user): bool
{
    return $user->hasAnyPermission([
        TicketTypePermissions::Create,
        TicketTypePermissions::Update,
        TicketTypePermissions::Delete,
    ]);
}
```

Role checks belong at the gate/middleware layer. Policies enforce granular permissions.

---

### 2. `access-dashboard` gate is role-based instead of permission-based

The gate hardcodes a role list. A custom role can never get dashboard access without modifying the gate definition itself.

```php
// Before — role list hardcoded in provider
Gate::define('access-dashboard', fn (User $user) =>
    $user->hasAnyRole(PreservedRoleList::adminRoles())
);

// After — permission-based, extensible to custom roles
Gate::define('access-dashboard', fn (User $user) =>
    $user->hasPermission(DashboardPermissions::Access)
);
```

Add `app/Enums/DashboardPermissions.php`:

```php
enum DashboardPermissions: string
{
    case Access = 'dashboard:access';
}
```

Seed `dashboard:access` onto `super-admin`, `admin`, and `organization-admin` in `PermissionSeeder`.

---

### 3. No request-level permission caching (`LoadPermissionsMiddleware` is missing)

`CLAUDE.md` references a `LoadPermissionsMiddleware` but it does not exist. `User::getAllPermissions()` calls `loadMissing('role.permissions')` — this prevents duplicate queries on the same model instance but not across re-fetched instances within the same request.

Create `app/Http/Middleware/LoadPermissionsMiddleware.php`:

```php
final class LoadPermissionsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $request->user()->load('role.permissions');
        }

        return $next($request);
    }
}
```

Register in the `web` middleware group in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        LoadPermissionsMiddleware::class,
    ]);
})
```

Once this exists, remove the `loadMissing` calls scattered in `User::getAllPermissions()` — permissions are guaranteed loaded before any policy runs.

---

### 4. `SharedPermissionResource` is manually maintained and exposes incomplete abilities

Every policy check must be manually listed. Forgetting an ability means the frontend makes silent authorization errors. The current surface is also incomplete:

```
organization: {viewAny, create}        ← missing update, delete
user:         {viewAny, create}        ← missing update, delete
role:         {viewAny, create}        ← missing update, delete
event:        {viewAny, create}        ← missing update, delete, publish, cancel
order:        {viewAny}                ← missing cancel
```

Fix both problems with a `checksFor()` helper that makes the resource declarative and forces you to enumerate all abilities explicitly:

```php
// Before — every line written by hand, easy to miss abilities
'event' => [
    'viewAny' => Gate::forUser($user)->check('viewAny', Event::class),
    'create'  => Gate::forUser($user)->check('create', Event::class),
],

// After — declarative, full surface exposed
'event' => $this->checksFor(Event::class, $user, ['viewAny', 'create', 'update', 'delete', 'publish', 'cancel']),
'order' => $this->checksFor(Order::class, $user, ['viewAny', 'cancel']),

private function checksFor(string $model, User $user, array $abilities): array
{
    return collect($abilities)
        ->mapWithKeys(fn (string $ability) => [
            $ability => Gate::forUser($user)->check($ability, $model),
        ])
        ->all();
}
```

---

### 5. `RoleAccessMiddleware` is registered but never used — YAGNI

Aliased as `'role'` in `bootstrap/app.php` but applied to zero routes.

- Delete `app/Http/Middleware/RoleAccessMiddleware.php`
- Remove the `'role'` alias from `bootstrap/app.php`

---

### 6. `Permission::grouped()` is presentation logic on a model

`Permission::grouped(?Role $role)` formats permissions into a UI-friendly structure. This is display/transform logic — it belongs in a Resource or Service, not a model.

```php
// Move to app/Services/PermissionService.php
public function grouped(?Role $role = null): array
{
    return Permission::all()
        ->groupBy(fn (Permission $permission) => explode(':', $permission->name)[0])
        ->map(fn (Collection $permissions, string $entity) => [
            'entity'      => $entity,
            'permissions' => $permissions,
            'hasAll'      => $role?->permissions
                ->pluck('id')
                ->intersect($permissions->pluck('id'))
                ->count() === $permissions->count(),
        ])
        ->values()
        ->all();
}
```

---

## Part 2 — General Architecture Issues (SOLID, DDD, Clean Architecture)

### 7. SRP — `User` model owns authorization logic

The `User` model carries identity, authentication, domain relationships, *and* authorization (`hasPermission`, `hasAnyRole`, `getAllPermissions`). Authorization is a distinct concern.

Extract an `AuthorizationChecker` service that handles permission queries. The model becomes a pure data carrier, and unit testing authorization logic requires no Eloquent model.

```
User model           → identity + relationships only
AuthorizationChecker → "can this user do X?"
```

---

### 8. DIP — Policies are coupled to `User` concrete methods

Every policy calls `$user->hasPermission()` directly on the Eloquent model. If the permission-checking strategy ever changes (Redis cache, JWT claims, external auth service), every policy must be updated.

Define an `Authorizable` contract that `User` implements. Policies depend on the interface, not the model:

```php
interface Authorizable
{
    public function hasPermission(string|BackedEnum $permission): bool;
    public function hasAnyPermission(array $permissions): bool;
}
```

Policies become testable with a simple stub — no database required.

---

### 9. Defense in Depth — Authorization is only enforced at the controller layer

`$this->authorize()` is called in controllers only. Services, console commands, and queued jobs bypass authorization entirely when calling service methods directly.

Critical write operations in service classes should assert authorization as a precondition:

```php
// Service layer enforces its own invariant
public function cancel(Order $order, Authorizable $actor): void
{
    throw_unless($actor->hasPermission(OrderPermissions::Cancel), AuthorizationException::class);
    // ...
}
```

Each layer defends itself independently — a job or command that calls the service directly cannot bypass the check.

---

### 10. Least Privilege — Organization scoping is ad-hoc across policies

`OrganizationAdmin` has `event:*` and `user:*` permissions globally. The restriction to "only within their organization" is enforced by manual ownership checks scattered across individual policies:

```php
// Repeated in EventPolicy, CheckInPolicy, and any future policy
$user->organization_id === $resource->organization_id
```

This will be forgotten in a future policy. Extract it as a shared abstraction:

```php
trait ScopedToOrganization
{
    protected function withinOrganization(User $user, mixed $resource): bool
    {
        return $user->organization_id === $resource->organization_id;
    }
}
```

Or better — a query scope that automatically filters by organization when the actor is an `OrganizationAdmin`, making the constraint structural rather than manually repeated.

---

### 11. CQRS Awareness — High-consequence write permissions are conflated

`event:update` gates publish, unpublish, cancel, and metadata edits — operations with very different risk profiles. A user who can edit event details probably shouldn't be able to cancel a live event with active ticket sales.

Split high-consequence operations into distinct permissions:

```
event:update  → edit metadata
event:publish → publish/unpublish
event:cancel  → cancel (irreversible, financial impact)
```

---

### 12. Audit Trail — Zero observability on authorization decisions

There is no logging of permission denials, role changes, or sensitive mutations.

- `Gate::after()` hook to log denials
- Model observers on `Role` and `Permission` for change tracking
- Explicit audit log entries on high-consequence actions (cancel order, assign role)

In a system governing access to financial and event data, an audit trail is a security requirement, not a nice-to-have.

---

## Part 3 — Full Refactor Opportunities (Development Mode)

### 13. SuperAdmin bypass via `Gate::before()` — seeding all permissions is fragile

Every new permission must be manually added to the seeder for super-admin. Forget one and super-admin silently loses access with no error.

`Gate::before()` short-circuits all policy checks for super-admin unconditionally. Returning `true` grants everything; returning `null` falls through to normal policy evaluation for everyone else:

```php
// AppServiceProvider::boot()
Gate::before(fn (User $user) =>
    $user->hasAnyRole(PreservedRoleList::SuperAdmin) ? true : null
);
```

Remove all super-admin permission assignments from `PermissionSeeder` — they are no longer needed.

---

### 14. `PermissionSeeder` duplicates enum values as hardcoded strings

The seeder creates permissions using raw strings (`'event:create'`), duplicating values already defined in the permission enums. If an enum value changes, the DB holds stale permission names that no enum can resolve — silently breaking all authorization.

Drive the seeder entirely from enum cases:

```php
$permissions = collect([
    ...EventPermissions::cases(),
    ...UserPermissions::cases(),
    ...RolePermissions::cases(),
    ...OrganizationPermissions::cases(),
    ...TicketTypePermissions::cases(),
    ...CheckInPermissions::cases(),
    ...OrderPermissions::cases(),
    ...SettingPermissions::cases(),
])->map(fn (BackedEnum $case) => ['name' => $case->value, 'description' => '']);

Permission::upsert($permissions->toArray(), ['name']);
```

Adding a new enum case automatically makes it available to seed. No string duplication anywhere.

---

### 15. Enum case naming — `AllowCreate` prefix is redundant

All permission enum cases are prefixed with `Allow`: `AllowCreate`, `AllowUpdate`, `AllowDelete`. The prefix adds no meaning — the enum *is* a permissions enum, every case already implies "allow".

```php
// Before
EventPermissions::AllowCreate
EventPermissions::AllowUpdate

// After
EventPermissions::Create
EventPermissions::Update
EventPermissions::Delete
```

Rename all cases across all permission enums. Do this before any other change touches enums so you aren't renaming twice.

---

### 16. No `view` permissions — read and write access are conflated

There are no `event:view`, `user:view`, or similar read permissions. `EventPolicy::viewAny()` currently grants list access to anyone who has *any* mutation permission. This means:

- A user with only `event:delete` can browse the full events list
- You cannot grant "view-only / read-only" access without also granting mutation rights
- Auditors, support staff, and reporting roles cannot exist

Add `View` to each permission enum. Update `viewAny()` and `view()` policies to check the read permission explicitly.

---

### 17. No TypeScript types for the `can` object — silent `undefined` at runtime

The frontend accesses the `can` object with no type safety. Accessing an ability that isn't exposed returns `undefined` silently — no compile-time error, no IDE warning.

Define a TypeScript interface that mirrors `SharedPermissionResource` exactly:

```ts
// resources/js/types/permissions.ts
export interface Can {
    organization: { viewAny: boolean; create: boolean; update: boolean; delete: boolean }
    user:         { viewAny: boolean; create: boolean; update: boolean; delete: boolean }
    role:         { viewAny: boolean; create: boolean; update: boolean; delete: boolean }
    event:        { viewAny: boolean; create: boolean; update: boolean; delete: boolean; publish: boolean; cancel: boolean }
    order:        { viewAny: boolean; cancel: boolean }
    setting:      { update: boolean }
    dashboard:    { access: boolean }
}
```

Register it on the global Inertia page props type so `usePage().props.can` is fully typed in every component.

---

### 18. Single role per user — consider `belongsToMany`

`User belongsTo Role` (one role, FK on `users` table). Real constraints:

- An org admin who also manages check-in needs two roles — impossible currently
- Temporary privilege escalation requires swapping the role and remembering to revert it
- "Users with both X and Y access" queries require joining through permissions

`belongsToMany(Role)` with a `user_roles` pivot resolves all of these. Permission resolution becomes the union of all assigned roles' permissions.

**Decide now** — this is the most invasive structural change. If your domain genuinely only needs four fixed roles and one per user, keep `belongsTo`. If you anticipate custom roles or combined access patterns, migrate to `belongsToMany`.

---

### 19. `Role::superAdminRole()` static methods fire individual DB queries on every call

Each call issues a `SELECT` query. Wrap with `once()` so the result is memoized per request:

```php
public static function superAdminRole(): self
{
    return once(fn () => self::query()->where('slug', PreservedRoleList::SuperAdmin->value)->firstOrFail());
}
```

---

### 20. `hasAnyRole()` accepts raw strings — typos fail silently

```php
$user->hasAnyRole('super-admen')  // typo → returns false, no error, no exception
```

Constrain the method to only accept `PreservedRoleList` enum cases:

```php
public function hasAnyRole(PreservedRoleList|array $roles): bool
{
    $slugs = collect(is_array($roles) ? $roles : [$roles])
        ->map(fn (PreservedRoleList $role) => $role->value);

    return $slugs->contains($this->role->slug);
}
```

A wrong slug becomes a type error at call time, not a silent `false` at runtime.

---

### 21. `EventPolicy::view()` is identical to `viewAny()` — remove it

`view(Event $event)` performs the exact same permission check as `viewAny()`. Laravel's `view()` is intended for ownership or visibility checks on a *single* resource. If it's just a clone, remove it.

---

## Part 4 — Bugs & Code Quality Violations

### 22. BUG — `Role::users()` declares `belongsToMany` but the schema is `hasMany`

`User` has `role_id` as a foreign key — that's a `belongsTo` / `hasMany` relationship. But `Role::users()` declares `belongsToMany`, which expects a `role_user` pivot table that doesn't exist. This will throw at runtime when the relationship is accessed.

```php
// Before — WRONG, looks for a role_user pivot table
public function users(): BelongsToMany
{
    return $this->belongsToMany(User::class);
}

// After — correct for the current schema
public function users(): HasMany
{
    return $this->hasMany(User::class);
}
```

Note: if #18 (migrate to `belongsToMany` roles) is implemented, this becomes correct — but a `user_roles` migration must exist first.

---

### 23. BUG — 6 permissions defined in enums but never seeded

The seeder creates 13 permissions. The enums define 19. These 6 are completely absent from the database:

| Missing permission | Enum | Policies broken |
|---|---|---|
| `check-in:manage` | `CheckInPermissions` | `EventPolicy::checkIn()`, `TicketPolicy::view()` |
| `order:view` | `OrderPermissions` | `OrderPolicy::viewAny()`, `OrderPolicy::view()` |
| `order:cancel` | `OrderPermissions` | `OrderPolicy::cancel()` |
| `ticket-type:create` | `TicketTypePermissions` | `TicketTypePolicy::create()` |
| `ticket-type:update` | `TicketTypePermissions` | `TicketTypePolicy::update()` |
| `ticket-type:delete` | `TicketTypePermissions` | `TicketTypePolicy::delete()` |

Since these permissions don't exist in the DB they are never assigned to any role — every policy check against them silently returns `false` for all users. Admins cannot view orders, check in attendees, or manage ticket types. Fixed by implementing #14 (drive seeder from enum cases).

---

### 24. BUG — `RolePolicy::update()` only protects SuperAdmin; `delete()` protects all preserved roles

```php
// update() — only SuperAdmin is protected
public function update(User $user, Role $role): bool
{
    return $user->hasPermission(RolePermissions::AllowUpdate)
        && $role->slug !== PreservedRoleList::SuperAdmin->value;
}

// delete() — ALL preserved roles protected
public function delete(User $user, Role $role): bool
{
    return $user->hasPermission(RolePermissions::AllowDelete) && ! $role->preserved;
}
```

An admin can freely rename the `admin` or `organization-admin` role and change their permissions. Only SuperAdmin is protected from modification. `delete()` uses `!$role->preserved` correctly — `update()` should too:

```php
public function update(User $user, Role $role): bool
{
    return $user->hasPermission(RolePermissions::AllowUpdate) && ! $role->preserved;
}
```

---

### 25. Unused parameters in policy methods — misleading signatures

Multiple policy methods declare a model parameter they never use, suggesting the method scopes to that resource when it doesn't:

```php
// EventPolicy — $event ignored in all three
public function publish(User $user, Event $event): bool   // $event unused
public function cancel(User $user, Event $event): bool    // $event unused
public function unpublish(User $user, Event $event): bool // $event unused

// UserPolicy — $target ignored
public function view(User $user, User $target): bool      // $target unused

// OrganizationPolicy — $organization ignored in both
public function approve(User $user, Organization $organization): bool // $organization unused
public function reject(User $user, Organization $organization): bool  // $organization unused
```

Either use the parameter (add ownership/scoping logic) or remove it. Unused parameters are a promise the code doesn't keep.

---

### 26. Manual policy registration in `AppServiceProvider` is redundant

Three policies are manually registered in `configurePolicies()`:

```php
Gate::policy(Order::class, OrderPolicy::class);
Gate::policy(Setting::class, SettingPolicy::class);
Gate::policy(Ticket::class, TicketPolicy::class);
```

Laravel auto-discovers policies that follow the `App\Models\Foo` → `App\Policies\FooPolicy` convention. All three satisfy this convention and would be found automatically. The manual registrations add no value but create the false impression that `EventPolicy`, `UserPolicy`, etc. are registered differently.

Remove all three manual registrations, or register every policy manually for explicitness — pick one approach and be consistent.

---

### 27. Untyped closure parameters in `hasAllPermissions()` and `hasAnyPermission()`

Both methods use an untyped `$permission` in their closure, violating the project's strict typing convention:

```php
// Both methods — untyped
->map(fn ($permission) => $permission instanceof BackedEnum ? $permission->value : $permission)

// Should match hasPermission()'s explicit type declaration
->map(fn (string|BackedEnum $permission) => $permission instanceof BackedEnum ? $permission->value : $permission)
```

---

### 28. `PreservedRoleList::adminRolesString()` is dead code

`adminRolesString()` returns a comma-separated string of role slugs — the exact format consumed by `RoleAccessMiddleware` for route definitions like `->middleware('role:admin,super-admin')`. Since `RoleAccessMiddleware` is dead code (#5), this method has no caller. Remove it alongside the middleware.

---

### 29. `mb_strtolower` in `getAllPermissions()` is redundant noise

```php
->map(fn (string $name): string => mb_strtolower($name));
```

All permission values in the DB are already lowercase by definition — the seeder stores them lowercase and every enum value is lowercase. This normalization is defensive without purpose. If case consistency matters, enforce it at the enum or DB constraint level — not silently at query time.

---

## Master Priority Table

| Priority | Issue | Part | Effort |
|----------|-------|------|--------|
| **Bug** | `Role::users()` wrong relationship type — will throw at runtime (#22) | Bugs | Trivial |
| **Bug** | 6 permissions missing from seeder — policies silently return false (#23) | Bugs | Low |
| **Bug** | `RolePolicy::update()` asymmetry — preserved roles unprotected (#24) | Bugs | Trivial |
| **Do first** | Rename `AllowCreate` → `Create` on all enums (#15) | Full Refactor | Low |
| **Do first** | Enums as single source of truth for seeder (#14) | Full Refactor | Low |
| **Do first** | `Gate::before()` for SuperAdmin (#13) | Full Refactor | Low |
| **Do first** | Add `LoadPermissionsMiddleware` (#3) | WWTD | Low |
| **Do first** | Fix `TicketTypePolicy::viewAny()` (#1) | WWTD | Low |
| **Structural** | Decide single vs multi-role `belongsToMany` (#18) | Full Refactor | High |
| **Structural** | Add `view` permissions — separate read from write (#16) | Full Refactor | Medium |
| **Structural** | Authorization only at controller layer (#9) | Architecture | Medium |
| **Structural** | Audit trail missing (#12) | Architecture | Medium |
| **Quality** | Expand `SharedPermissionResource` (#4) | WWTD | Low |
| **Quality** | TypeScript types for `can` (#17) | Full Refactor | Low |
| **Quality** | Type-safe `hasAnyRole()` with enum (#20) | Full Refactor | Low |
| **Quality** | Replace role-based dashboard gate (#2) | WWTD | Low |
| **Quality** | Organization scoping is ad-hoc (#10) | Architecture | Medium |
| **Quality** | `User` model owns authorization logic (#7) | Architecture | Medium |
| **Quality** | Policies coupled to `User` concrete methods (#8) | Architecture | Medium |
| **Quality** | Fix unused policy method parameters (#25) | Bugs | Low |
| **Quality** | Split high-consequence write permissions (#11) | Architecture | Low |
| **Quality** | Cache `Role::superAdminRole()` with `once()` (#19) | Full Refactor | Trivial |
| **Quality** | Add types to `hasAllPermissions`/`hasAnyPermission` closures (#27) | Bugs | Trivial |
| **Cleanup** | Remove redundant manual policy registrations (#26) | Bugs | Trivial |
| **Cleanup** | Remove duplicate `EventPolicy::view()` (#21) | Full Refactor | Trivial |
| **Cleanup** | Delete `RoleAccessMiddleware` + `adminRolesString()` (#5, #28) | WWTD | Trivial |
| **Cleanup** | Remove `mb_strtolower` from `getAllPermissions()` (#29) | Bugs | Trivial |
| **Cleanup** | Move `Permission::grouped()` out of model (#6) | WWTD | Low |

**Fix bugs first** (#22, #23, #24) — two of these are silent runtime failures active right now. Then do the "Do first" group, which sets the foundation every other change depends on.
