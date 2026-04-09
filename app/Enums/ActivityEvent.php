<?php

declare(strict_types=1);

namespace App\Enums;

enum ActivityEvent: string
{
    case EventPublished = 'event.published';
    case EventCancelled = 'event.cancelled';
    case OrganizationApproved = 'organization.approved';
    case OrganizationRejected = 'organization.rejected';
    case OrderCancelled = 'order.cancelled';
    case RefundProcessed = 'refund.processed';

    public function label(): string
    {
        return match ($this) {
            self::EventPublished => 'Event Published',
            self::EventCancelled => 'Event Cancelled',
            self::OrganizationApproved => 'Organization Approved',
            self::OrganizationRejected => 'Organization Rejected',
            self::OrderCancelled => 'Order Cancelled',
            self::RefundProcessed => 'Refund Processed',
        };
    }
}
