---
name: wwtd
description: >-
    Apply "What Would Taylor Otwell Do?" (WWTD) as a decision-making lens
    for Laravel architecture, conventions, and code quality questions.
    Activate when the user asks about best practices, naming conventions,
    code structure, or when choosing between multiple approaches.
---

# WWTD — What Would Taylor Otwell Do?

## When to activate

Use this skill when the user:

- Asks about Laravel conventions or best practices
- Is choosing between multiple implementation approaches
- Wants to know the idiomatic Laravel way to do something
- Asks "what would Taylor do?" or "WWTD"

## Core principles

When answering through the WWTD lens, prioritise these values
(derived from Taylor Otwell's philosophy and Laravel's design):

1. **Convention over configuration.** Laravel has opinions. Follow them.
   Plural relationship names, `snake_case` columns, resourceful controllers.
2. **Use what the framework gives you.** Before writing a helper, check if
   Laravel already has one (`Str::random()`, `e()`, `data_get()`, etc).
3. **Simplicity wins.** The best solution is usually the one with the fewest
   lines, the least abstraction, and no premature optimisation.
4. **YAGNI.** Don't build for hypothetical future requirements. Remove unused
   code rather than commenting it out.
5. **Explicit over magic.** Prefer explicit query constraints over global
   scopes. Prefer `$fillable` over `$guarded = []`. Make intent visible at
   the call site.
6. **Work with the framework, not against it.** If a solution requires
   workarounds, hidden props, or event bridges — you're fighting Laravel.
   Step back and use the components as designed.

## Response format

When this skill is activated, structure your response as:

1. **The situation** — what the user is trying to do
2. **What Taylor would do** — the idiomatic Laravel answer, with reasoning
3. **The fix** — concrete before/after code if applicable
4. **The takeaway** — what principle this teaches about Laravel

## Anti-patterns to flag

- Custom utility classes that duplicate framework helpers
- `$guarded = []` on models (use explicit `$fillable` instead)
- Global scopes where explicit constraints would be clearer
- `strip_tags()` for security (use `e()` for escaping)
- ServiceProviders used as utility classes
- Complex conditional logic for features that don't exist yet
- Hidden Livewire components bridging UI frameworks

## Investigation template

For non-trivial questions, follow this structured approach:

1. Explain the issue in simple terms
2. Investigate 2–3 possible solutions
3. Pick a recommendation with clear pros and cons
4. Check the official Laravel documentation
5. What would Taylor Otwell do — and why?
