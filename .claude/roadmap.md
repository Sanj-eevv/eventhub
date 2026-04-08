# Feature Roadmap (WWTD)

Identified 2026-04-08. All items use existing Laravel primitives — no new packages or patterns needed.
Each item targets a gap in flows that already have partial infrastructure.

---

## Priority Order

### 1. Reserved Order Expiry — Scheduled Command
- `orders:expire-reserved` — cancel `Reserved` orders older than 15 minutes
- Prevents ghost reservations blocking ticket capacity
- `TicketStatus::Pending` and `OrderStatus::Reserved` already exist
- Wire in `routes/console.php` with `Schedule::command()->everyFifteenMinutes()`

### 2. Horizon Dashboard Lock-Down
- `laravel/horizon` is installed but dashboard is unprotected
- Add to `AppServiceProvider::configurePolicies()`:
  ```php
  Gate::define('viewHorizon', fn (User $user) => $user->hasAnyRole(PreservedRoleList::SuperAdmin));
  ```
- Schedule `horizon:snapshot` every 5 minutes for queue metrics

### 3. Stripe Refund Webhook Handler
- `RefundStatus` enum exists (Pending, Completed, Failed)
- `ProcessRefundAction` exists
- `StripeWebhookController` exists
- Missing: handler for Stripe's `charge.refunded` event to mark `RefundStatus::Completed`
- The data model is ready — the loop just needs closing

### 4. Event Reminder Notifications
- Notify ticket holders 24hrs before event starts
- `SendOrderConfirmationNotification` is the existing pattern to follow
- Use Laravel Notifications with `mail` + `database` channels
- Schedule `events:remind` command daily

### 5. Ticket Cancellation & Refund Confirmation Notifications
- Customer receives no notification when their order/ticket is cancelled
- Add: `OrderCancelled → SendOrderCancelledNotification`
- Add: `RefundCompleted → SendRefundConfirmationNotification`

### 6. In-App Notification Bell
- `database` notification channel gives this for free once notifications are wired
- `User` model already has `Notifiable` trait and `notifications` relationship

### 7. Refund Admin UI
- `ProcessRefundAction` exists but no dashboard UI to trigger it
- Admin has no way to initiate refunds from the dashboard
- Add dedicated `RefundOrderController`

### 8. Activity / Audit Log
- `Gate::after()` already logs denied authorizations — pattern exists
- Add `activity_log` table with polymorphic `subject`
- Track: published event, approved org, cancelled order, processed refund

### 9. Public Attendee Ticket Page
- `TicketQrCodeController` exists — full ticket view is one step away
- Show ticket details, QR code, event info in one page

### 10. Event Search & Filtering
- Browse page (`BrowseEventController`) exists but has no date/location filtering
- Waitlist when `TicketType` capacity is full

### 11. Telescope (Local Dev Only)
- Install `laravel/telescope` restricted to non-production
- Valuable for inspecting Stripe webhook payloads, job payloads, slow queries

---

## Audit Fixes Applied (2026-04-08)
- `UpdateEventAction:76` — `is_active` fatal bug removed
- `UpdateEventAction` — `description` not synced on ticket type update fixed
