<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketTypePermissions: string
{
    case AllowCreate = 'ticket-type:create';
    case AllowUpdate = 'ticket-type:update';
    case AllowDelete = 'ticket-type:delete';
}
