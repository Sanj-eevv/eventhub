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

### 2. Domain events for side effects
`ConfirmOrganizationStatusAction` calls `Mail::queue()` directly. Email,
webhooks, and audit logging should be side effects of named domain events
(`OrganizationApproved`, `OrganizationRejected`, `EventPublished`), not
inlined in actions. Actions change state; listeners react.

**Files:** `app/Actions/ConfirmOrganizationStatusAction.php`

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

---

## Low Priority

### 9. No audit trail for sensitive operations
Role permission changes, organisation status changes, and user role assignments
leave no record. Needed for any compliance requirement.

**Files:** `app/Actions/UpdateRoleAction.php`,
`app/Actions/ConfirmOrganizationStatusAction.php`

