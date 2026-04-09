# Feature Roadmap (WWTD)

Identified 2026-04-08. All items use existing Laravel primitives — no new packages or patterns needed.
Each item targets a gap in flows that already have partial infrastructure.

---

## Priority Order

### 4. Event Reminder Notifications

- Notify ticket holders 24hrs before event starts
- `SendOrderConfirmationNotification` is the existing pattern to follow
- Use Laravel Notifications with `mail` + `database` channels
- Schedule `events:remind` command daily

