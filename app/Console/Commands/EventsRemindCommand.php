<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\SendEventRemindersAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('events:remind')]
#[Description('Send 24-hour reminder notifications to ticket holders for upcoming events')]
final class EventsRemindCommand extends Command
{
    public function handle(SendEventRemindersAction $action): void
    {
        $action->execute();
        $this->info('Event reminder notifications dispatched.');
    }
}
