<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class EventCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Event $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("Event Cancelled — {$this->event->title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("We're sorry to let you know that **{$this->event->title}** has been cancelled.")
            ->line('If you purchased tickets, your order has been voided. Please contact support regarding any refund questions.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Event Cancelled',
            'body' => "{$this->event->title} has been cancelled.",
            'url' => null,
        ];
    }
}
