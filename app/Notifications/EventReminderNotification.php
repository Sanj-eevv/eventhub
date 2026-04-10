<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class EventReminderNotification extends Notification implements ShouldQueue
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
            ->subject("Reminder: {$this->event->title} is tomorrow")
            ->greeting("Hi {$notifiable->name},")
            ->line("This is a reminder that **{$this->event->title}** starts tomorrow.")
            ->line("Venue: {$this->event->venue_name}, {$this->event->address}")
            ->action('View Order', route('orders.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Event Reminder',
            'body' => "{$this->event->title} starts tomorrow.",
            'url' => route('orders.index'),
        ];
    }
}
