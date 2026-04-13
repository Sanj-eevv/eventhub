<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\RoleData;
use App\Models\Role;
use Illuminate\Database\DatabaseManager;

final readonly class CreateRoleAction
{
    public function __construct(private DatabaseManager $databaseManager) {}

    public function __invoke(RoleData $data): Role
    {
        return $this->databaseManager->transaction(function () use ($data): Role {
            $role = Role::query()->create([
                'name' => $data->name,
                'description' => $data->description,
            ]);
            $role->permissions()->sync($data->permissions ?? []);

            return $role;
        });
    }
}
