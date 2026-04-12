<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\DatabaseManager;

final readonly class DeleteRoleAction
{
    public function __construct(private DatabaseManager $databaseManager) {}

    public function execute(Role $role): void
    {
        $this->databaseManager->transaction(function () use ($role): void {
            User::withTrashed()->where('role_id', $role->id)->update([
                'role_id' => Role::userRole()->id,
            ]);

            $role->delete();
        });
    }
}
