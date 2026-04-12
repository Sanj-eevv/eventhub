<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrganizationApproved;
use App\Mail\Organizations\StatusApproved;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

final readonly class SendOrganizationApprovedMail implements ShouldQueue
{
    public function __construct(private Mailer $mailer) {}

    public function shouldQueue(OrganizationApproved $event): bool
    {
        return $event->notify;
    }

    public function handle(OrganizationApproved $event): void
    {
        $this->mailer->to($event->organization->contact_email)->queue(new StatusApproved($event->organization));
    }
}
