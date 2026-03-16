## Mandatory Laravel Code Simplification

- Always run the laravel-simplifier plugin on every Laravel/PHP code snippet you generate to ensure the code is clean, optimized, and follows Laravel best practices.
- Do not write inline or block comments in generated code.
- Only include DocBlock comments when strictly necessary (e.g., for type hints, generics, or framework conventions).

## Stateless by Design

Favor explicitness over hidden state.

- Avoid hidden dependencies in models, services, actions, and helpers.
- Pass required data explicitly through DTOs, method parameters, and return values.
- Avoid implicit context, magic side effects, and stateful behavior that is not visible at the call site.
- Prefer explicit query building over implicit scopes when business constraints or filters matter.
- Make inputs, outputs, and state transitions easy to trace.
- Optimize for predictability, readability, and testability.
