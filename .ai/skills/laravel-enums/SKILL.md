---
name: laravel-enums
description: Backed enums with labels and business logic. Use when working with enums, status values, fixed sets of options, or when user mentions enums, backed enums, enum cases, status enums.
---

# Laravel Enums

Enums provide type-safe, finite sets of values.

## Always Use Backed Enums

**Always use backed enums** (string or int):

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
```

## Usage in Validation

```php
use Illuminate\Validation\Rules\Enum;

'status' => [
    'required',
    'string',
    'bail',
    new Enum(OrderStatus::class),
],
```

## Common Patterns

### Comparison

```php
if ($order->status->is(OrderStatus::Completed)) {
    // ...
}
```

### Match Expressions

```php
$message = match ($order->status) {
    OrderStatus::Reserved => 'Your order is reserved',
    OrderStatus::Processing => 'We are processing your order',
    OrderStatus::Completed => 'Your order is complete',
    OrderStatus::Cancelled => 'Your order was cancelled',
};
```

## Queue Enum Example

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum Queue: string
{
    case Default = 'default';
    case Processing = 'processing';
    case Emails = 'emails';
    case Notifications = 'notifications';
}
```

**Usage in jobs:**

```php
public function __construct(public Order $order)
{
    $this->onQueue(Queue::Emails->value);
}
```

**Enums provide:**

- Type safety
- Finite value sets
- Business logic encapsulation
- IDE autocomplete

**Always use backed enums** with string or int values.
