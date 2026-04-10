<?php

declare(strict_types=1);

namespace App\Enums;

enum ActivityEvent: string
{
    case EventPublished = 'event.published';
    case EventCancelled = 'event.cancelled';
    case OrganizationApproved = 'organization.approved';
    case OrganizationRejected = 'organization.rejected';
    case OrderCompleted = 'order.completed';
    case OrderCancelled = 'order.cancelled';
    case PaymentFailed = 'order.payment_failed';
    case RefundProcessed = 'refund.processed';
    case TicketCheckedIn = 'ticket.checked_in';
    case QrCodeGenerationFailed = 'ticket.qr_code_failed';

    public function label(): string
    {
        return match ($this) {
            self::EventPublished => 'Event Published',
            self::EventCancelled => 'Event Cancelled',
            self::OrganizationApproved => 'Organization Approved',
            self::OrganizationRejected => 'Organization Rejected',
            self::OrderCompleted => 'Order Completed',
            self::OrderCancelled => 'Order Cancelled',
            self::PaymentFailed => 'Payment Failed',
            self::RefundProcessed => 'Refund Processed',
            self::TicketCheckedIn => 'Ticket Checked In',
            self::QrCodeGenerationFailed => 'QR Code Generation Failed',
        };
    }
}
