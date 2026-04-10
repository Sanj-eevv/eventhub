# Codebase Roadmap

Audit of what Taylor Otwell would introduce or improve in this project.
Status tracked as work progresses.

---

## 🔴 Critical

### #1 — Zero Test Coverage
No tests exist. Priority areas:
- Feature tests for controllers (auth, checkout, orders, events)
- Unit tests for all 37 actions
- Policy authorization tests
- Form request validation tests

### #3 — Model Observers (`app/Observers/` is empty)
- `OrderObserver` — cache invalidation on status change
- `EventObserver` — capacity tracking, publish/draft transitions
- `TicketObserver` — check-in logging

---

## 🟠 High Priority

### #6 — Missing Database Indexes
Foreign keys and status columns on high-traffic tables lack indexes:
```php
$table->index(['event_id', 'status']); // tickets, orders
$table->index('organization_id');
```

### #9 — More Value Objects
- `Money` — prices are plain `int`, prone to arithmetic bugs
- `DateRange` — event start/end dates passed as separate fields
- `Percentage` — refund percentage in settings

### #10 — Exception Handler Customization
Domain exceptions (`InsufficientTicketCapacityException`, etc.) surface as 500s in production instead of user-friendly responses. Need mapping in `bootstrap/app.php` `withExceptions`.

### #11 — Missing `PaymentFailedNotification`
`payment_intent.payment_failed` Stripe webhook event has no handler. Users are not notified when payment fails.

---

## 🟡 Medium Priority

- **Cursor pagination** on builders for large ticket/order result sets
- **`ScopedToOrganization`** inconsistency — not applied uniformly across all org-owned models
- **`refund_status`** in `OrderResource` serialized as plain string — should use `{value, label, color}` like other statuses
- **`TicketResource` status** serialized as plain string — upgrade to `{value, label, color}` (requires frontend updates in `My/Orders/Show`, `Dashboard/Orders/Show`, `Checkout/Confirmation`)
