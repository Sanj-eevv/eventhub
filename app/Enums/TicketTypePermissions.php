<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketTypePermissions: string
{
    case Create = 'ticket-type:create';
    case Update = 'ticket-type:update';
    case Delete = 'ticket-type:delete';
}
