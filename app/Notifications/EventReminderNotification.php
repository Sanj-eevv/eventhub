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

    /** @return string[] */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(sprintf('Reminder: %s is tomorrow', $this->event->title))
            ->greeting(sprintf('Hi %s,', $notifiable->name))
            ->line(sprintf('This is a reminder that **%s** starts tomorrow.', $this->event->title))
            ->line(sprintf('Venue: %s, %s', $this->event->venue_name, $this->event->address))
            ->action('View Order', route('orders.index'));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Event Reminder',
            'body' => $this->event->title.' starts tomorrow.',
            'url' => route('orders.index'),
        ];
    }
}
