# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Backend
```bash
php artisan test              # Run all tests
php artisan test --filter=TestName  # Run a single test
composer test                 # Alias for php artisan test
./vendor/bin/pest             # Run Pest directly
./vendor/bin/pint             # Fix PHP code style
php artisan ide-helper:generate  # Regenerate IDE helper files
```

### Frontend
```bash
npm run dev    # Start Vite dev server
npm run build  # Production build
npx eslint .   # Lint frontend code
```

### Wayfinder (after adding/changing routes)
```bash
php artisan wayfinder:generate  # Regenerate type-safe route bindings in resources/js/wayfinder/
```

## Architecture

This is a **Laravel 12 + Vue 3 + Inertia.js** application. The backend serves as an API-like layer using Inertia rather than REST/JSON endpoints.

### Backend Layers

**Request flow:** Route → Middleware → Controller → Service → Model → Resource (for response)

- **Actions** (`app/Actions/`) — single-responsibility classes for discrete operations (CreateUserAction, LoginAction, etc.)
- **Services** (`app/Services/`) — business logic (UserService, RoleService, PermissionService, OrganizationService, EventService)
- **DTOs** (`app/DataTransferObjects/`) — typed data transfer between layers
- **Resources** (`app/Http/Resources/`) — transform Eloquent models for Inertia responses; each entity has `IndexResource` and `ShowResource` variants
- **Policies** (`app/Policies/`) — authorization; registered in AppServiceProvider

### RBAC System

Three preserved system roles defined in `PreservedRoleList` enum: `SUPER_ADMIN`, `ADMIN`, `ORGANIZATION_ADMIN`. Permissions follow a `entity:action` format (e.g., `user:create`, `event:edit`) and are defined in enums under `app/Enums/` (EventPermissions, UserPermissions, etc.).

Permission loading: `LoadPermissionsMiddleware` caches permissions per request; `HandleInertiaRequests` shares them with the frontend via `SharedPermissionResource`. User model has `hasPermission()`, `hasAnyPermission()`, `hasAnyRole()`, and `getAllPermissions()` methods.

### Frontend Architecture

Pages live in `resources/js/pages/` and map directly to Inertia responses. Components are organized as:
- `resources/js/components/ui/` — Shadcn-Vue primitives (built on Reka UI), never modified directly
- `resources/js/components/Dashboard/` — feature-specific components
- `resources/js/composables/` — Vue composables organized by domain (events/, organizations/, users/, roles/)

**Routing:** Use Wayfinder (`resources/js/wayfinder/`) for type-safe route references in frontend code. Import from `@/wayfinder` or `@/actions`.

**Forms:** Use `useForm` from `@inertiajs/vue3`. Precognitive validation is used on registration forms.

**Tables:** Use the `DataTable.vue` component built on TanStack Vue Table with `@tanstack/vue-table`.

**Toasts:** Use `vue-sonner` for notifications; flash messages from Inertia shared data trigger toasts automatically.

### Key Middleware

- `HandleInertiaRequests` — shares auth user, permissions, sidebar state, and flash messages with all Inertia responses
- `RoleAccessMiddleware` — restricts dashboard routes to allowed roles
- `LoadPermissionsMiddleware` — loads and caches user permissions

### Testing

Tests use Pest 4 with the Laravel plugin. The test database is SQLite in-memory. Feature tests are in `tests/Feature/`, unit tests in `tests/Unit/`.

### Models & Traits

All primary models use `HasAppUuid` (UUID primary keys) and relevant models use `HasSlug`. Models have strict mode enabled and lazy loading is disabled globally (set in AppServiceProvider).

Rate limiting is configured centrally in `AppServiceProvider::boot()`.

===

<laravel-boost-guidelines>
=== .ai/Coding rules ===

## Mandatory Laravel Code Simplification

- Always run the laravel-simplifier plugin on every Laravel/PHP code snippet you generate to ensure the code is clean, optimized, and follows Laravel best practices.
- Do not write inline or block comments in generated code.
- Only include DocBlock comments when strictly necessary (e.g., for type hints, generics, or framework conventions).

## Actions

- Always activate the `laravel-actions` skill when creating or updating any action class in `app/Actions/`.

## Guard Placement in Actions vs Controllers

When a conditional check (guard) exists before calling an action, decide where it belongs based on whether it affects the HTTP response:

- **Guard goes in the action** — when it only determines whether the action should do its work, with no effect on the controller's response. The controller calls the action unconditionally.
- **Guard stays in the controller** — when it produces a different redirect or response (e.g. early return to a different route). The guard is part of the response logic, not the action's concern.

## Controllers — Required Structure

Every controller must follow these rules without exception:

1. **Extend `Controller`** — always `extends Controller` (provides `AuthorizesRequests`).
2. **RESTful methods only** — resource controllers may only contain: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`. No custom method names (e.g. `cover`, `publish`, `approve`).
3. **Invokable controllers for non-RESTful actions** — any action that does not map to a standard RESTful method must be its own `final class FooController extends Controller` with a single `__invoke()` method.
4. **Authorize every method** — call `$this->authorize()` as the first line of every public method.
5. **Flash `toast_success` on all redirects** — every redirect must chain `.with('toast_success', '...')`.

```php
// ✅ Non-RESTful action → invokable controller
final class SetEventMediaCoverController extends Controller
{
    public function __invoke(Event $event, Media $media): RedirectResponse
    {
        $this->authorize('update', $event);

        $this->setCoverAction->execute($event, $media);

        return $this->redirector->back()->with('toast_success', 'Cover image updated successfully.');
    }
}
```

## Controllers — No Global Helpers

Controllers must never use global helper functions for HTTP concerns. Always use the injected dependency instead.

- Use `$this->redirector->back()` not `back()`
- Use `$this->redirector->route()` not `redirect()->route()`
- Use `$this->urlGenerator->route()` not `route()`

When reviewing controllers, always check for `back()`, `redirect()`, `route()`, and `url()` global calls — these are implicit dependencies and must be replaced.

## Enums

- Do not install packages for enum functionality (e.g. `henzeb/enumhancer`).
- Do not use reflection-based label infrastructure (`#[Label]` attribute + `HasLabel` trait).
- Use a plain `match` expression for `label()` methods when human-readable labels are needed.

## Laravel-First Coding Style

This project follows Laravel architecture practices. Always ask "What Would Taylor Do?" when making decisions.

- **Never use `array_map`, `array_filter`, or `array_reduce`** — always use Laravel Collection methods (`->map()`, `->filter()`, `->reduce()`, etc.).
- **Never use single-character or abbreviated variable names** in closures or anywhere else (e.g. `$p`, `$s`, `$v`). Use descriptive names that reflect the domain (`$permission`, `$sort`, `$value`). This is a Spatie PHP guideline and is mandatory.
- Prefer `collect()` over raw PHP array functions throughout the codebase.
- Always activate the `wwtd` skill when choosing between multiple approaches or making architectural decisions.

## PHP Imports

- Always use `use` statements to import PHP classes, interfaces, enums, and global namespace classes (e.g. `use DateTimeZone;`).
- Never reference classes with a leading backslash (e.g. `\DateTimeZone`) — always import them at the top of the file.
- This applies to PHP built-ins, global classes, and all userland classes without exception.

## Conditionals

- Compound `&&` conditions are acceptable in `if` statements, return expressions, and closures. Do not split them into nested `if` blocks.

## Wayfinder Route Imports

- Always import routes from `@/wayfinder/routes/` (named route imports), not from `@/wayfinder/App/Http/Controllers/` (controller imports).
- Named route imports are domain-grouped and hide controller implementation details from the frontend.
- Example: `import { approve, reject } from "@/wayfinder/routes/dashboard/organizations"` — not `import ApproveOrganizationController from "@/wayfinder/App/Http/Controllers/Dashboard/ApproveOrganizationController"`.

## Stateless by Design

Favor explicitness over hidden state.

- Avoid hidden dependencies in models, services, actions, and helpers.
- Pass required data explicitly through DTOs, method parameters, and return values.
- Avoid implicit context, magic side effects, and stateful behavior that is not visible at the call site.
- Prefer explicit query building over implicit scopes when business constraints or filters matter.
- Make inputs, outputs, and state transitions easy to trace.
- Optimize for predictability, readability, and testability.

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v3
- laravel/framework (LARAVEL) - v13
- laravel/horizon (HORIZON) - v5
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- laravel/wayfinder (WAYFINDER) - v
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/vue3 (INERTIA_VUE) - v3
- @laravel/vite-plugin-wayfinder (WAYFINDER_VITE) - v0
- eslint (ESLINT) - v9
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `configuring-horizon` — Use this skill whenever the user mentions Horizon by name in a Laravel context. Covers the full Horizon lifecycle: installing Horizon (horizon:install, Sail setup), configuring config/horizon.php (supervisor blocks, queue assignments, balancing strategies, minProcesses/maxProcesses), fixing the dashboard (authorization via Gate::define viewHorizon, blank metrics, horizon:snapshot scheduling), and troubleshooting production issues (worker crashes, timeout chain ordering, LongWaitDetected notifications, waits config). Also covers job tagging and silencing. Do not use for generic Laravel queues without Horizon, SQS or database drivers, standalone Redis setup, Linux supervisord, Telescope, or job batching.
- `wayfinder-development` — Activates whenever referencing backend routes in frontend components. Use when importing from @/actions or @/routes, calling Laravel routes from TypeScript, or working with Wayfinder route functions.
- `pest-testing` — Tests applications using the Pest 4 PHP framework. Activates when writing tests, creating unit or feature tests, adding assertions, testing Livewire components, browser testing, debugging test failures, working with datasets or mocking; or when the user mentions test, spec, TDD, expects, assertion, coverage, or needs to verify functionality works.
- `inertia-vue-development` — Develops Inertia.js v3 Vue client-side applications. Activates when creating Vue pages, forms, or navigation; using <Link>, <Form>, useForm, useHttp, setLayoutProps, or router; working with deferred props, prefetching, optimistic updates, instant visits, or polling; or when user mentions Vue with Inertia, Vue pages, Vue forms, or Vue navigation.
- `tailwindcss-development` — Always invoke when the user's message includes 'tailwind' in any form. Also invoke for: building responsive grid layouts (multi-column card grids, product grids), flex/grid page structures (dashboards with sidebars, fixed topbars, mobile-toggle navs), styling UI components (cards, tables, navbars, pricing sections, forms, inputs, badges), adding dark mode variants, fixing spacing or typography, and Tailwind v3/v4 work. The core use case: writing or fixing Tailwind utility classes in HTML templates (Blade, JSX, Vue). Skip for backend PHP logic, database queries, API routes, JavaScript with no HTML/CSS component, CSS file audits, build tool configuration, and vanilla CSS.
- `design-an-interface` — Generate multiple radically different interface designs for a module using parallel sub-agents. Use when user wants to design an API, explore interface options, compare module shapes, or mentions "design it twice".
- `frontend-design` — Create distinctive, production-grade frontend interfaces with high design quality. Use this skill when the user asks to build web components, pages, artifacts, posters, or applications (examples include websites, landing pages, dashboards, React components, HTML/CSS layouts, or when styling/beautifying any web UI). Generates creative, polished code and UI design that avoids generic AI aesthetics.
- `grill-me` — Interview the user relentlessly about a plan or design until reaching shared understanding, resolving each branch of the decision tree. Use when user wants to stress-test a plan, get grilled on their design, or mentions "grill me".
- `improve-codebase-architecture` — Explore a codebase to find opportunities for architectural improvement, focusing on making the codebase more testable by deepening shallow modules. Use when user wants to improve architecture, find refactoring opportunities, consolidate tightly-coupled modules, or make a codebase more AI-navigable.
- `laravel-actions` — Action-oriented architecture for Laravel. Invokable classes that contain domain logic. Use when working with business logic, domain operations, or when user mentions actions, invokable classes, or needs to organize domain logic outside controllers.
- `laravel-architecture` — High-level architecture decisions, patterns, and project structure. Use when user asks about architecture decisions, project structure, pattern selection, or mentions how to organize, which pattern to use, best practices, architecture.
- `laravel-controllers` — Thin HTTP layer controllers. Controllers contain zero domain logic, only HTTP concerns. Use when working with controllers, HTTP layer, web vs API patterns, or when user mentions controllers, routes, HTTP responses.
- `laravel-enums` — Backed enums with labels and business logic. Use when working with enums, status values, fixed sets of options, or when user mentions enums, backed enums, enum cases, status enums.
- `laravel-models` — Eloquent model patterns and database layer. Use when working with models, database entities, Eloquent ORM, or when user mentions models, eloquent, relationships, casts, observers, database entities.
- `laravel-query-builders` — Custom query builders for type-safe, composable database queries. Use when working with database queries, query scoping, or when user mentions query builders, custom query builder, query objects, query scopes, database queries.
- `laravel-routes-best-practices` — Keep routes clean and focused on mapping requests to controllers; avoid business logic, validation, or database operations in route files
- `laravel-specialist` — Build and configure Laravel 10+ applications, including creating Eloquent models and relationships, implementing Sanctum authentication, configuring Horizon queues, designing RESTful APIs with API resources, and building reactive interfaces with Livewire. Use when creating Laravel models, setting up queue workers, implementing Sanctum auth flows, building Livewire components, optimising Eloquent queries, or writing Pest/PHPUnit tests for Laravel features.
- `laravel-validation` — Form request validation and comprehensive validation testing. Use when working with validation rules, form requests, validation testing, or when user mentions validation, form requests, validation rules, conditional validation, validation testing.
- `laravel-value-objects` — Immutable value objects for domain values. Use when working with domain values, immutable objects, or when user mentions value objects, immutable values, domain values, money objects, coordinate objects.
- `php-guidelines-from-spatie` — Describes PHP and Laravel guidelines provided by Spatie. These rules result in more maintainable, and readable code.
- `shadcn-vue` — shadcn-vue for Vue/Nuxt with Reka UI components and Tailwind. Use for accessible UI, Auto Form, data tables, charts, dark mode, MCP server setup, or encountering component imports, Reka UI errors.
- `vue-best-practices` — MUST be used for Vue.js tasks. Strongly recommends Composition API with `<script setup>` and TypeScript as the standard approach. Covers Vue 3, SSR, Volar, vue-tsc. Load for any Vue, .vue files, Vue Router, Pinia, or Vite with Vue work. ALWAYS use Composition API unless the project explicitly requires Options API.
- `wwtd` — Apply "What Would Taylor Otwell Do?" (WWTD) as a decision-making lens for Laravel architecture, conventions, and code quality questions. Activate when the user asks about best practices, naming conventions, code structure, or when choosing between multiple approaches.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan Commands

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`, `php artisan tinker --execute "..."`).
- Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Debugging

- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.
- To execute PHP code for debugging, run `php artisan tinker --execute "your code here"` directly.
- To read configuration values, read the config files directly or run `php artisan config:show [key]`.
- To inspect routes, run `php artisan route:list` directly.
- To check environment variables, read the `.env` file directly.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd and will be available at: `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs for the user.
- You must not run any commands to make the site available via HTTP(S). It is always available through Laravel Herd.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== wayfinder/core rules ===

# Laravel Wayfinder

Wayfinder generates TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

- IMPORTANT: Activate `wayfinder-development` skill whenever referencing backend routes in frontend components.
- Invokable Controllers: `import StorePost from '@/actions/.../StorePostController'; StorePost()`.
- Parameter Binding: Detects route keys (`{post:slug}`) — `show({ slug: "my-post" })`.
- Query Merging: `show(1, { mergeQuery: { page: 2, sort: null } })` merges with current URL, `null` removes params.
- Inertia: Use `.form()` with `<Form>` component or `form.submit(store())` with useForm.

=== wayfinder/v rules ===

# Laravel Wayfinder

Wayfinder generates TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

- IMPORTANT: Activate `wayfinder-development` skill whenever referencing backend routes in frontend components.
- Invokable Controllers: `import StorePost from '@/actions/.../StorePostController'; StorePost()`.
- Parameter Binding: Detects route keys (`{post:slug}`) — `show({ slug: "my-post" })`.
- Query Merging: `show(1, { mergeQuery: { page: 2, sort: null } })` merges with current URL, `null` removes params.
- Inertia: Use `.form()` with `<Form>` component or `form.submit(store())` with useForm.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

</laravel-boost-guidelines>
