# Architecture Findings

## Strengths (No Action Needed)

- **Actions** — 21 well-organized classes, proper DI, no business logic leaking out
- **DTOs** — all readonly, no primitive passing, smart transformation methods
- **Controllers** — pure HTTP layer, zero domain logic
- **Query Builders** — type-safe, whitelist-based sorting via AppBuilder hierarchy
- **Strict typing** — `declare(strict_types=1)` in every file
- **RBAC** — well-structured permission caching via Context

---

## High Priority

### 1. State machines for status transitions
`EventStatus` and `OrganizationStatus` allow any transition right now. State
validation is scattered between policies and actions with no single enforcer.
A state machine value object should own valid transitions and make illegal
states unrepresentable.

**Valid transitions to enforce:**
- Event: `Draft → Published`, `Published → Cancelled`, `Published → Draft` (unpublish)
- Organization: `Pending → Approved`, `Pending → Rejected`, `Approved → Suspended`

**Files:** `app/Enums/EventStatus.php`, `app/Enums/OrganizationStatus.php`,
`app/Actions/UpdateEventStatusAction.php`, `app/Actions/ConfirmOrganizationStatusAction.php`,
`app/Policies/EventPolicy.php`, `app/Policies/OrganizationPolicy.php`

### 2. Domain events for side effects
`ConfirmOrganizationStatusAction` calls `Mail::queue()` directly. Email,
webhooks, and audit logging should be side effects of named domain events
(`OrganizationApproved`, `OrganizationRejected`, `EventPublished`), not
inlined in actions. Actions change state; listeners react.

**Files:** `app/Actions/ConfirmOrganizationStatusAction.php`

### 3. `PermissionService` is misclassified
Per architecture guidelines, services are for external integrations.
`PermissionService::getGroupedPermissions()` is a UI transformation — groups
permissions by entity for display. It belongs as a resource or dedicated
transformer class, not a service.

**File:** `app/Services/PermissionService.php`

---

## Medium Priority

### 4. Value objects for unmodelled domain concepts
`location` and `tickets` on `EventData` are raw arrays. `email` is a raw
string everywhere. These have invariants that value objects would enforce at
construction time.

- `Location` — venue name, address, coordinates
- `Ticket` — label, price (non-negative), quantity, sale window
- `Email` — format validation at the type level

**Files:** `app/DataTransferObjects/EventData.php`, all DTOs with email fields

### 5. No custom exception hierarchy
All failures surface as generic `ModelNotFoundException` from `firstOrFail()`.
Domain errors should be named and catchable.

- `InvalidStatusTransitionException`
- `RoleHasActiveUsersException`
- `PreservedRoleException`

### 6. `OrganizationData` couples two concerns
The optional `?UserData $user` field on `OrganizationData` ties organisation
creation to user creation. This coupling only exists for the registration flow.
The DTO should represent only organisation data; the registration workflow
should compose them separately.

**File:** `app/DataTransferObjects/OrganizationData.php`

### 7. `DeleteRoleAction` has no null guard on fallback role
`Role::userRole()` can return `null` if the user role doesn't exist. The
action calls `->id` on it directly — null dereference in production if the
role is missing.

**File:** `app/Actions/DeleteRoleAction.php`

---

## Low Priority

### 8. Missing builder methods for common queries
Controllers repeat inline query constraints that belong on the builders.

- `EventBuilder::byOrganization(Organization $org)`
- `EventBuilder::byUser(User $user)`
- `UserBuilder::byOrganization(Organization $org)`

**Files:** `app/Builders/EventBuilder.php`, `app/Builders/UserBuilder.php`

### 9. No `ActionInterface` contract
Action signatures are unenforceable. An `execute()` contract would make the
pattern explicit and allow polymorphic use in tests or pipelines.

### 10. No audit trail for sensitive operations
Role permission changes, organisation status changes, and user role assignments
leave no record. Needed for any compliance requirement.

**Files:** `app/Actions/UpdateRoleAction.php`,
`app/Actions/ConfirmOrganizationStatusAction.php`

---

## Completed

*(none yet — these are all open)*
