<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrganizationRejected;
use App\Mail\Organizations\StatusRejected;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

final readonly class SendOrganizationRejectedMail implements ShouldQueue
{
    public function __construct(private Mailer $mailer) {}

    public function shouldQueue(OrganizationRejected $event): bool
    {
        return $event->notify;
    }

    public function handle(OrganizationRejected $event): void
    {
        $this->mailer->to($event->organization->contact_email)->queue(new StatusRejected($event->organization));
    }
}
