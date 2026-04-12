<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Organization;
use Illuminate\Database\DatabaseManager;

final readonly class DeleteOrganizationAction
{
    public function __construct(private DatabaseManager $databaseManager) {}

    public function execute(Organization $organization): void
    {
        $this->databaseManager->transaction(function () use ($organization): void {
            $organization->users()->delete();
            $organization->delete();
        });
    }
}
