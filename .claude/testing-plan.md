# Testing Plan

**Stack:** Pest 4 · Laravel Feature Tests · SQLite in-memory · `RefreshDatabase`
**Convention:** No framework internals tested. No testing that Eloquent saves or that Laravel dispatches jobs — only YOUR business rules.
**Stripe:** `PaymentGateway` contract is already abstracted. Bind a `FakePaymentGateway` in the container. Zero Stripe SDK calls in tests.

---

## Infrastructure to Create First

### `tests/Pest.php` — Updated Configuration

```php
pest()
    ->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()
    ->extend(TestCase::class)
    ->in('Unit');
```

### `tests/Fakes/FakePaymentGateway.php`

Implements `App\Contracts\PaymentGateway`. Returns configurable `PaymentIntentResult`. Tracks calls for assertion. Bound in `TestCase::setUp()` via `$this->app->bind(PaymentGateway::class, FakePaymentGateway::class)`.

---

## Execution Order

Tests are written and run in this exact sequence — from first user action to last system behaviour.

---

## 1. Architecture Tests

**File:** `tests/Architecture/ArchTest.php`

| Test | Assertion |
|------|-----------|
| All action classes are final | `arch()->expect('App\Actions')->toBeFinal()` |
| All action classes have one public method named `execute` | `arch()->expect('App\Actions')->toHaveMethod('execute')` |
| Controllers extend `App\Http\Controllers\Controller` | `arch()->expect('App\Http\Controllers')->toExtend(Controller::class)` |
| No controller uses global helpers `back()`, `redirect()`, `route()`, `url()` | `arch()->expect('App\Http\Controllers')->not->toUse(['back', 'redirect', 'route', 'url'])` |
| All models use `HasAppUuid` | `arch()->expect('App\Models')->toUse(HasAppUuid::class)` |
| Value objects are readonly | `arch()->expect('App\ValueObjects')->toBeReadonly()` |
| DTOs are readonly | `arch()->expect('App\DataTransferObjects')->toBeReadonly()` |
| No `DB::` facade used anywhere | `arch()->expect('App')->not->toUse('Illuminate\Support\Facades\DB')` |
| Enums are backed enums | `arch()->expect('App\Enums')->toBeStringBackedEnum()` or `toBeIntBackedEnum()` as appropriate |
| Policies extend `App\Policies\BasePolicy` | `arch()->expect('App\Policies')->toExtend(BasePolicy::class)->ignoring(BasePolicy::class)` |

---

## 2. Unit Tests — Value Objects

### `tests/Unit/ValueObjects/MoneyTest.php`

| Test | What it asserts |
|------|----------------|
| `fromCents` creates instance with correct cents and currency | `Money::fromCents(1000, 'USD')->cents === 1000` |
| `format` returns correctly formatted string | `Money::fromCents(12345, 'USD')->format() === '123.45 USD'` |
| `applyPercentage` with 100% returns same amount | `money->applyPercentage(Percentage::fromInt(100))->cents === original` |
| `applyPercentage` with 50% returns half amount | `money->applyPercentage(Percentage::fromInt(50))->cents === original / 2` |
| `applyPercentage` with 0% returns zero | `money->applyPercentage(Percentage::fromInt(0))->cents === 0` |

### `tests/Unit/ValueObjects/PercentageTest.php`

| Test | What it asserts |
|------|----------------|
| `fromInt` accepts 0 | no exception |
| `fromInt` accepts 100 | no exception |
| `fromInt` below 0 throws `InvalidArgumentException` | exception thrown |
| `fromInt` above 100 throws `InvalidArgumentException` | exception thrown |
| `applyTo` 1000 at 50% returns 500 | `Percentage::fromInt(50)->applyTo(1000) === 500` |
| `applyTo` floors the result (integer division) | `Percentage::fromInt(33)->applyTo(100) === 33` |

### `tests/Unit/ValueObjects/DateRangeTest.php`

| Test | What it asserts |
|------|----------------|
| End before start throws `InvalidArgumentException` | exception thrown |
| End equal to start throws `InvalidArgumentException` | exception thrown |
| `contains` returns true for date within range | date between start and end |
| `contains` returns false for date before range | date before start |
| `contains` returns false for date after range | date after end |

### `tests/Unit/ValueObjects/BookingReferenceTest.php`

| Test | What it asserts |
|------|----------------|
| `generate` produces string starting with `EVT-` | prefix check |
| `generate` produces string of correct total length | `EVT-` + 6 chars = 10 chars |
| `generate` is unique across multiple calls | two generated references are not equal |
| `__toString` returns the string value | cast to string works |

---

## 3. Unit Tests — Enums

### `tests/Unit/Enums/EventStatusTest.php`

| Test | What it asserts |
|------|----------------|
| Draft can transition to Published | `canTransitionTo(Published) === true` |
| Draft cannot transition to Cancelled | `canTransitionTo(Cancelled) === false` |
| Published can transition to Draft | `canTransitionTo(Draft) === true` |
| Published can transition to Cancelled | `canTransitionTo(Cancelled) === true` |
| Cancelled is final — cannot transition to anything | all `canTransitionTo` return false |
| `isFinal` returns false for Draft and Published | assertion |
| `isFinal` returns true for Cancelled | assertion |
| Each case returns a non-empty `label()` | string is not empty |
| Each case returns a non-empty `color()` | string is not empty |

### `tests/Unit/Enums/OrderStatusTest.php`

| Test | What it asserts |
|------|----------------|
| Reserved can transition to Paid | true |
| Reserved can transition to Cancelled | true |
| Paid can transition to Cancelled | true |
| Paid cannot transition to Reserved | false |
| Cancelled is final | all transitions false |
| `isFinal` is true only for Cancelled | assertion |

### `tests/Unit/Enums/TicketStatusTest.php`

| Test | What it asserts |
|------|----------------|
| Pending can transition to Active | true |
| Pending can transition to Cancelled | true |
| Pending cannot transition to Used | false |
| Active can transition to Used | true |
| Active can transition to Cancelled | true |
| Used is final | all transitions false |
| Cancelled is final | all transitions false |

### `tests/Unit/Enums/OrganizationStatusTest.php`

| Test | What it asserts |
|------|----------------|
| Pending can transition to Approved | true |
| Pending can transition to Rejected | true |
| Approved can transition to Suspended | true |
| Rejected is final | all transitions false |
| Suspended is final | all transitions false |

---

## 4. Feature Tests — Authentication

### `tests/Feature/Auth/RegistrationTest.php`

| Test | What it asserts |
|------|----------------|
| Guest can view the registration page | `assertSuccessful()` on GET `/register` |
| Authenticated user is redirected away from registration | `assertRedirect()` |
| Guest can register with valid data | user is created, redirected, authenticated |
| Registration requires name | validation error on `name` |
| Registration requires valid email | validation error on `email` |
| Registration requires unique email | validation error when email already taken |
| Registration requires password confirmation | validation error on `password` |
| Password must meet minimum length | validation error when too short |
| Registered user has `User` role assigned | `$user->role->slug === 'user'` |
| Email verification notification is sent on registration | `Notification::assertSentTo()` |

### `tests/Feature/Auth/OrganizationRegistrationTest.php`

| Test | What it asserts |
|------|----------------|
| Guest can view the organization registration page | `assertSuccessful()` |
| Guest can register an organization with valid data | organization + user created, status is Pending |
| Organization registration requires organization name | validation error |
| Organization registration requires contact email | validation error |
| Organization registration requires unique contact email | validation error |
| Registering user is assigned `OrganizationAdmin` role | role check |
| Organization is created with `Pending` status | `$organization->status === OrganizationStatus::Pending` |
| Registered user is linked to the new organization | `$user->organization_id` matches |

### `tests/Feature/Auth/LoginTest.php`

| Test | What it asserts |
|------|----------------|
| Guest can view login page | `assertSuccessful()` |
| Authenticated user is redirected away from login | `assertRedirect()` |
| User can login with correct credentials | `assertAuthenticated()` |
| Login fails with wrong password | `assertGuest()`, validation errors |
| Login fails with unknown email | validation errors |
| Login is rate-limited after 5 attempts by email | `assertTooManyRequests()` or 429 |

### `tests/Feature/Auth/LogoutTest.php`

| Test | What it asserts |
|------|----------------|
| Authenticated user can logout | `assertGuest()`, redirected |
| Guest cannot logout (POST /logout) | redirected to login |

### `tests/Feature/Auth/EmailVerificationTest.php`

| Test | What it asserts |
|------|----------------|
| Unverified user sees verification notice | `assertSuccessful()` on GET `/email/verify` |
| Unverified user is redirected to verification notice on protected routes | `assertRedirectContains('/email/verify')` |
| User can verify email via signed URL | `email_verified_at` is set, redirected |
| Expired or invalid signed URL returns 403 | `assertForbidden()` |
| User can request resend of verification email | `Notification::assertSentTo()` |
| Resend is rate-limited | check rate limit applies |
| Already verified user clicking verify link is redirected without error | redirect to dashboard or home |

### `tests/Feature/Auth/PasswordResetTest.php`

| Test | What it asserts |
|------|----------------|
| Guest can view forgot password page | `assertSuccessful()` |
| Guest can request a password reset link | `Notification::assertSentTo()` |
| Request with unknown email returns no error (security) | no validation error exposed |
| Password reset request is rate-limited | applies |
| Guest can view the reset password form via valid token | `assertSuccessful()` |
| Invalid or expired token shows error | appropriate response |
| User can reset password with valid token | password is changed, authenticated |
| Reset password requires password confirmation | validation error |

---

## 5. Feature Tests — Public Browsing

### `tests/Feature/Browse/HomeTest.php`

| Test | What it asserts |
|------|----------------|
| Homepage loads for guests | `assertSuccessful()` |
| Homepage loads for authenticated users | `assertSuccessful()` |

### `tests/Feature/Browse/BrowseEventsTest.php`

| Test | What it asserts |
|------|----------------|
| Guest can browse published events | `assertSuccessful()`, published events in response |
| Draft events are not visible to guests | draft event not in response data |
| Cancelled events are not visible to guests | cancelled event not in response data |
| Events can be filtered by search term | only matching events returned |
| Individual published event page loads | `assertSuccessful()` |
| Individual draft event returns 404 for guests | `assertNotFound()` |
| Individual cancelled event returns 404 for guests | `assertNotFound()` |

---

## 6. Feature Tests — Ticket Reservation

### `tests/Feature/Checkout/ReserveTicketsTest.php`

| Test | What it asserts |
|------|----------------|
| Guest is redirected to login when reserving | `assertRedirectToRoute('login')` |
| Authenticated user can reserve tickets for a published event | order created with `Reserved` status, tickets created as `Pending`, `ExpireOrderJob` dispatched |
| Reservation creates correct number of tickets | `$order->tickets()->count() === $quantity` |
| Reservation sets expiry based on settings `ticketReservationMinutes` | `$order->expires_at` is within expected window |
| Cannot reserve tickets for a draft event | `EventNotAvailableException` → redirected back with toast error |
| Cannot reserve tickets for a cancelled event | same as above |
| Cannot reserve when user has an active reservation for same event | `ActiveReservationExistsException` → redirect with toast |
| Cannot exceed ticket type capacity | `InsufficientTicketCapacityException` → redirect with toast |
| Cannot exceed `max_per_user` limit | `TicketLimitExceededException` → redirect with toast |
| Cannot reserve when ticket sale has not started | `TicketSaleNotOpenException` → redirect with toast |
| Cannot reserve when ticket sale has closed | `TicketSaleClosedException` → redirect with toast |
| Reservation request validates required `ticket_types` array | validation errors |
| Reservation request validates positive quantity | validation errors |

### `tests/Feature/Checkout/CheckoutTest.php`

| Test | What it asserts |
|------|----------------|
| Authenticated user can view their checkout page | `assertSuccessful()`, Stripe publishable key present in Inertia props |
| Another user cannot view someone else's checkout | `assertForbidden()` |
| Viewing a paid order's checkout redirects to confirmation | `assertRedirectToRoute('checkout.confirmation', ...)` |
| Viewing an expired/cancelled order's checkout redirects | redirect with appropriate message |
| User can cancel a reserved order | order deleted or cancelled, redirected |
| User cannot cancel another user's order | `assertForbidden()` |
| User cannot cancel a paid order via the cancel-reservation endpoint | appropriate guard |

### `tests/Feature/Checkout/PaymentTest.php`

| Test | What it asserts |
|------|----------------|
| User can process payment for their own reserved order | `FakePaymentGateway::createPaymentIntent` called, `stripe_payment_intent_id` stored on order |
| Processing payment for already-paid order is idempotent | no duplicate intent created |
| Another user cannot process payment on someone else's order | `assertForbidden()` |

### `tests/Feature/Checkout/ConfirmationTest.php`

| Test | What it asserts |
|------|----------------|
| User can view confirmation for their own paid order | `assertSuccessful()`, order data in props |
| User cannot view confirmation for another user's order | `assertForbidden()` |
| Viewing confirmation for an unpaid order returns 404 or redirects | appropriate response |

---

## 7. Feature Tests — Stripe Webhooks

### `tests/Feature/Webhooks/StripeWebhookTest.php`

Strategy: `HandleStripeWebhookController` uses `Stripe\Webhook::constructEvent()` for signature verification. For tests, bind a partial mock or pass a signed payload using the test webhook secret set in config.

For `HandleStripeWebhookAction` behaviour, construct `Stripe\Event` directly via `Stripe\Event::constructFrom([...])` and call the action — no HTTP needed.

| Test | What it asserts |
|------|----------------|
| Webhook with invalid signature returns 400 | `assertStatus(400)` on POST `/webhooks/stripe` with bad signature |
| `payment_intent.succeeded` event completes the matching order | order status becomes `Paid`, tickets become `Active`, `OrderConfirmedNotification` sent, `GenerateTicketQrCodesJob` dispatched |
| `payment_intent.succeeded` for unknown payment intent is silently ignored | no exception, 204 |
| `payment_intent.succeeded` for already-paid order is idempotent | `CompleteOrderAction` guards — order remains `Paid`, no duplicate notification |
| `payment_intent.payment_failed` records activity and notifies user | `ActivityEvent::PaymentFailed` logged, `PaymentFailedNotification` sent |
| `payment_intent.payment_failed` for unknown payment intent is silently ignored | no exception |
| Unknown webhook event type is ignored | 204, no side effects |

---

## 8. Feature Tests — My Orders

### `tests/Feature/Orders/MyOrdersTest.php`

| Test | What it asserts |
|------|----------------|
| Guest is redirected to login | `assertRedirectToRoute('login')` |
| Authenticated user can view their own orders index | `assertSuccessful()`, only their orders in response |
| Another user's orders are not visible | isolation check |
| User can view their own order detail | `assertSuccessful()`, correct order data |
| User cannot view another user's order | `assertForbidden()` |

### `tests/Feature/Orders/CancelOrderTest.php`

| Test | What it asserts |
|------|----------------|
| User can cancel their own reserved (unpaid) order | order is deleted/cancelled, redirect with success toast |
| User cannot cancel another user's reserved order | `assertForbidden()` |
| User can request cancellation of their own paid order | order marked for refund, `ProcessRefundJob` dispatched |
| Paid order cancellation is blocked within `cancellationCutoffHours` | redirect with toast error |
| User cannot cancel another user's paid order | `assertForbidden()` |
| Cancelling an already-cancelled order is blocked | appropriate guard |
| QR code file is deleted when paid order is cancelled | `Storage::assertMissing()` |

---

## 9. Feature Tests — Notifications (Frontend)

### `tests/Feature/Notifications/NotificationsTest.php`

| Test | What it asserts |
|------|----------------|
| Guest is redirected to login | redirect |
| Authenticated user can view their notifications | `assertSuccessful()` |
| User can mark a single notification as read | notification `read_at` is set |
| User cannot mark another user's notification as read | `assertForbidden()` |
| User can mark all notifications as read | all unread `read_at` values are set |
| Marking all read on empty notifications list succeeds | no exception, 200/redirect |

---

## 10. Feature Tests — Dashboard Access

### `tests/Feature/Dashboard/DashboardTest.php`

| Test | What it asserts |
|------|----------------|
| Guest is redirected to login | redirect |
| Regular user without dashboard permission is forbidden | `assertForbidden()` |
| Admin with dashboard permission can access dashboard | `assertSuccessful()` |
| SuperAdmin can access dashboard | `assertSuccessful()` |
| Dashboard renders key stats in Inertia props | stats keys present |

---

## 11. Feature Tests — Dashboard / Organization Management

### `tests/Feature/Dashboard/Organizations/OrganizationManagementTest.php`

| Test | What it asserts |
|------|----------------|
| Admin can list organizations | `assertSuccessful()` |
| User without permission cannot list organizations | `assertForbidden()` |
| Admin can view a single organization | `assertSuccessful()` |
| Admin can create an organization | organization created with correct data, redirect with toast |
| Create validates required fields (title, contact_email) | validation errors |
| Create rejects duplicate contact email | validation error |
| Admin can update an organization | data persisted, redirect |
| Admin can delete an organization | soft-deleted, redirect |
| Admin can approve a pending organization | status becomes `Approved`, `OrganizationApproved` event fired |
| Approving an already-approved organization throws invalid status transition | redirect with toast error |
| Admin can reject a pending organization | status becomes `Rejected`, `OrganizationRejected` event fired |
| Rejecting an already-rejected organization throws invalid status transition | redirect with toast error |
| OrganizationAdmin can only see their own organization | scoped response |
| OrganizationAdmin cannot approve/reject | `assertForbidden()` |

---

## 12. Feature Tests — Dashboard / User Management

### `tests/Feature/Dashboard/Users/UserManagementTest.php`

| Test | What it asserts |
|------|----------------|
| Admin can list users | `assertSuccessful()` |
| User without permission cannot list users | `assertForbidden()` |
| Admin can create a user | user created, redirect |
| Create validates required fields | validation errors |
| Create rejects duplicate email | validation error |
| Admin can update a user | data persisted |
| Admin can delete a user | soft-deleted |
| Cannot delete a user who owns events (restrictOnDelete FK) | exception or appropriate guard |

---

## 13. Feature Tests — Dashboard / Role Management

### `tests/Feature/Dashboard/Roles/RoleManagementTest.php`

| Test | What it asserts |
|------|----------------|
| Admin can list roles | `assertSuccessful()` |
| User without permission cannot list roles | `assertForbidden()` |
| Admin can create a role with permissions | role created, permissions synced |
| Create validates required name field | validation error |
| Admin can update a role's permissions | `roles_permissions` pivot updated |
| Cannot delete a preserved role (`SuperAdmin`, `Admin`, etc.) | guard or exception |
| Admin can delete a non-preserved role | deleted, redirect |

---

## 14. Feature Tests — Dashboard / Event CRUD

### `tests/Feature/Dashboard/Events/EventCrudTest.php`

| Test | What it asserts |
|------|----------------|
| Admin can list events | `assertSuccessful()` |
| OrganizationAdmin sees only their organization's events | scoped index |
| User without permission cannot list events | `assertForbidden()` |
| Admin can view create event form | `assertSuccessful()`, organizations and timezones in props |
| Admin can create an event with ticket types | event created, ticket types created, redirect to edit with `focus=media` |
| Create validates required fields (title, starts_at, ends_at, timezone, ticket_types) | validation errors |
| Create rejects end date before start date | validation error |
| Admin can view the event edit form | `assertSuccessful()` |
| Admin can update an event | data persisted, ticket types synced |
| Update deletes removed ticket types | removed ticket type no longer exists |
| Admin can view event detail | `assertSuccessful()` |
| Admin can delete an event | soft-deleted, redirect |
| OrganizationAdmin cannot delete another organization's event | `assertForbidden()` |

### `tests/Feature/Dashboard/Events/EventStatusTest.php`

| Test | What it asserts |
|------|----------------|
| Admin can publish a draft event that has a cover image | status becomes `Published`, activity logged |
| Publishing a draft event without a cover image is blocked | `MissingEventCoverImageException` → redirect with toast |
| Publishing an already-published event is blocked | `InvalidStatusTransitionException` → redirect with toast |
| Admin can unpublish a published event | status becomes `Draft` |
| Cannot unpublish a draft event | invalid transition → redirect with toast |
| Admin can cancel a published event | status becomes `Cancelled`, `EventCancelled` event fired, activity logged |
| Cancelling a cancelled event is blocked | invalid transition |
| Cancelling event sends cancellation notifications to all paid-order customers | `Notification::assertSentTo()` for each customer |
| OrganizationAdmin cannot publish another org's event | `assertForbidden()` |

### `tests/Feature/Dashboard/Events/EventMediaTest.php`

| Test | What it asserts |
|------|----------------|
| Admin can upload media to their event | media record created, `ProcessEventMedia` job dispatched |
| Upload is rejected when event already has 10 files | `MediaLimitExceededException` → redirect with toast |
| Admin can delete a media file | media record deleted, file removed from disk |
| Admin can set a media file as the cover image | `is_cover` set to true, previous cover unset |
| OrganizationAdmin cannot upload media to another org's event | `assertForbidden()` |
| Attempting to set non-existent media as cover returns 404 | `assertNotFound()` |

### `tests/Feature/Dashboard/Events/CheckInTest.php`

| Test | What it asserts |
|------|----------------|
| User with `event:check-in` permission can view the check-in interface | `assertSuccessful()` |
| User without permission cannot view check-in interface | `assertForbidden()` |
| Scanning a valid `Active` ticket marks it as `Used` | `status === TicketStatus::Used`, `checked_in_at` set, `checked_in_by` set, activity logged |
| Scanning an already-used ticket returns invalid transition error | redirect with toast error |
| Scanning a cancelled ticket returns invalid transition error | redirect with toast error |
| Scanning a ticket for a non-published event returns error | redirect with toast (`RuntimeException` message) |
| Scanning with an invalid booking reference returns 404 | `assertNotFound()` |
| OrganizationAdmin can only check in tickets for their own events | scoped guard |

---

## 15. Feature Tests — Dashboard / Order Management

### `tests/Feature/Dashboard/Orders/OrderManagementTest.php`

| Test | What it asserts |
|------|----------------|
| Admin can list all orders | `assertSuccessful()` |
| OrganizationAdmin sees only their organization's event orders | scoped |
| User without permission cannot list orders | `assertForbidden()` |
| Admin can view order detail | `assertSuccessful()` |
| Admin can cancel a paid order (initiate refund) | `ProcessRefundJob` dispatched, redirect |
| Admin cancelling order uses settings refund percentage | correct amount calculated |

---

## 16. Feature Tests — Dashboard / Settings

### `tests/Feature/Dashboard/Settings/SettingsTest.php`

| Test | What it asserts |
|------|----------------|
| Admin can view the settings form | `assertSuccessful()`, current settings in props |
| User without `setting:update` permission cannot view settings | `assertForbidden()` |
| Admin can update `ticketReservationMinutes` | value persisted in settings table |
| Admin can update `cancellationCutoffHours` | value persisted |
| Admin can update `refundPercentage` | value persisted |
| Refund percentage must be between 0 and 100 | validation error |
| `ticketReservationMinutes` must be a positive integer | validation error |
| Settings service cache is invalidated after update | subsequent reads return new value |

---

## 17. Feature Tests — Jobs

### `tests/Feature/Jobs/ExpireOrderJobTest.php`

| Test | What it asserts |
|------|----------------|
| Handling the job with a `Reserved` order deletes the order and its tickets | order gone, tickets gone |
| Handling the job with a `Paid` order does nothing | order untouched |
| Handling the job with a `Cancelled` order does nothing | no exception |
| Job has `deleteWhenMissingModels = true` — if order is already deleted, job silently completes | no exception on missing model |

### `tests/Feature/Jobs/GenerateTicketQrCodesJobTest.php`

| Test | What it asserts |
|------|----------------|
| Handling the job generates a QR code SVG file for each ticket | `Storage::assertExists()` for each ticket's path |
| Each ticket's `qr_code_path` is updated after generation | `$ticket->fresh()->qr_code_path` is not null |
| Job failure calls `RecordActivityAction` with `QrCodeGenerationFailed` | activity log entry created |
| Job has `tries = 3` | assertion via job properties |

### `tests/Feature/Jobs/ProcessRefundJobTest.php`

| Test | What it asserts |
|------|----------------|
| Handling the job calls `PaymentGateway::refundPaymentIntent` with correct amount | `FakePaymentGateway` tracks the call |
| Order's `stripe_refund_id` and `refund_status` are updated | `$order->fresh()->refund_status === RefundStatus::Refunded` |
| `RefundCompletedNotification` is sent to order owner | `Notification::assertSentTo()` |
| Activity is logged with `RefundProcessed` event | activity log check |
| Refund amount respects settings `refundPercentage` when no explicit amount given | calculated amount passed to gateway |
| Explicit refund amount override bypasses percentage calculation | exact amount passed to gateway |

---

## Helper Traits & Base Classes to Create

| File | Purpose |
|------|---------|
| `tests/Fakes/FakePaymentGateway.php` | Implements `PaymentGateway`. Stores calls. Returns stubbed `PaymentIntentResult`. Configurable to throw exceptions. |
| `tests/Traits/CreatesUsers.php` | Helpers: `createSuperAdmin()`, `createAdmin()`, `createOrganizationAdmin(Organization)`, `createUser()` — using factories with correct role states. |
| `tests/Traits/CreatesEvents.php` | Helpers: `createPublishedEvent()`, `createDraftEvent()`, `createEventWithMedia()` — using factories. |
| `tests/Traits/CreatesOrders.php` | Helpers: `createReservedOrder(User, Event)`, `createPaidOrder(User, Event)` — using factories with correct states. |

---

## What is Explicitly NOT Tested

| Skipped | Reason |
|---------|--------|
| `DownloadOrderTicketsPdfController` | `spatie/browsershot` requires headless Chromium — not available in test env. Covered by smoke/manual testing. |
| `ProcessEventMedia` job (image conversion) | `intervention/image-laravel` WebP conversion is a third-party operation. Test only that the job is dispatched. |
| Stripe signature verification in `HandleStripeWebhookController` | `Webhook::constructEvent()` is Stripe SDK code. Test only that invalid signature → 400 at HTTP level; test action behaviour independently. |
| Framework middleware internals (`LoadPermissionsMiddleware` cache logic) | Framework concern. Test that permissions are correctly enforced at the endpoint level instead. |
| Eloquent relationship wiring | Framework concern. Test business outcomes (data in DB, correct response), not that `hasMany` works. |

---

## File Structure

```
tests/
├── Architecture/
│   └── ArchTest.php
├── Unit/
│   ├── Enums/
│   │   ├── EventStatusTest.php
│   │   ├── OrderStatusTest.php
│   │   ├── OrganizationStatusTest.php
│   │   └── TicketStatusTest.php
│   └── ValueObjects/
│       ├── BookingReferenceTest.php
│       ├── DateRangeTest.php
│       ├── MoneyTest.php
│       └── PercentageTest.php
├── Fakes/
│   └── FakePaymentGateway.php
├── Traits/
│   ├── CreatesEvents.php
│   ├── CreatesOrders.php
│   └── CreatesUsers.php
├── Feature/
│   ├── Auth/
│   │   ├── EmailVerificationTest.php
│   │   ├── LoginTest.php
│   │   ├── LogoutTest.php
│   │   ├── OrganizationRegistrationTest.php
│   │   ├── PasswordResetTest.php
│   │   └── RegistrationTest.php
│   ├── Browse/
│   │   ├── BrowseEventsTest.php
│   │   └── HomeTest.php
│   ├── Checkout/
│   │   ├── CheckoutTest.php
│   │   ├── ConfirmationTest.php
│   │   ├── PaymentTest.php
│   │   └── ReserveTicketsTest.php
│   ├── Orders/
│   │   ├── CancelOrderTest.php
│   │   └── MyOrdersTest.php
│   ├── Notifications/
│   │   └── NotificationsTest.php
│   ├── Webhooks/
│   │   └── StripeWebhookTest.php
│   ├── Dashboard/
│   │   ├── DashboardTest.php
│   │   ├── Events/
│   │   │   ├── CheckInTest.php
│   │   │   ├── EventCrudTest.php
│   │   │   ├── EventMediaTest.php
│   │   │   └── EventStatusTest.php
│   │   ├── Orders/
│   │   │   └── OrderManagementTest.php
│   │   ├── Organizations/
│   │   │   └── OrganizationManagementTest.php
│   │   ├── Roles/
│   │   │   └── RoleManagementTest.php
│   │   ├── Settings/
│   │   │   └── SettingsTest.php
│   │   └── Users/
│   │       └── UserManagementTest.php
│   └── Jobs/
│       ├── ExpireOrderJobTest.php
│       ├── GenerateTicketQrCodesJobTest.php
│       └── ProcessRefundJobTest.php
├── Pest.php
└── TestCase.php
```

---

## Count

| Layer | Files | Approximate test cases |
|-------|-------|----------------------|
| Architecture | 1 | 9 |
| Unit — ValueObjects | 4 | 20 |
| Unit — Enums | 4 | 26 |
| Feature — Auth | 6 | 36 |
| Feature — Browse | 2 | 9 |
| Feature — Checkout | 4 | 21 |
| Feature — Orders | 2 | 10 |
| Feature — Notifications | 1 | 6 |
| Feature — Webhooks | 1 | 7 |
| Feature — Dashboard | 9 | 65 |
| Feature — Jobs | 3 | 15 |
| **Total** | **37** | **~224** |
