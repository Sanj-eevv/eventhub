<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\RoleData;
use App\Models\Role;
use Illuminate\Database\DatabaseManager;

final readonly class UpdateRoleAction
{
    public function __construct(private DatabaseManager $databaseManager) {}

    public function execute(Role $role, RoleData $data): Role
    {
        return $this->databaseManager->transaction(function () use ($role, $data): Role {
            $role->update([
                'name' => $data->name,
                'description' => $data->description,
            ]);
            $role->permissions()->sync($data->permissions ?? []);

            return $role;
        });
    }
}
