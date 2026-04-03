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
