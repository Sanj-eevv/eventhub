<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CheckInPermissions;
use App\Enums\DashboardPermissions;
use App\Enums\EventPermissions;
use App\Enums\OrderPermissions;
use App\Enums\OrganizationPermissions;
use App\Enums\RolePermissions;
use App\Enums\SettingPermissions;
use App\Enums\TicketTypePermissions;
use App\Enums\UserPermissions;
use App\Models\Permission;
use App\Models\Role;
use BackedEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $allCases = collect([
                ...EventPermissions::cases(),
                ...UserPermissions::cases(),
                ...RolePermissions::cases(),
                ...OrganizationPermissions::cases(),
                ...TicketTypePermissions::cases(),
                ...CheckInPermissions::cases(),
                ...OrderPermissions::cases(),
                ...SettingPermissions::cases(),
                ...DashboardPermissions::cases(),
            ]);

            Permission::upsert(
                $allCases->map(fn (BackedEnum $case) => ['name' => $case->value, 'description' => ''])->all(),
                ['name'],
                ['updated_at'],
            );

            $permissions = Permission::query()->pluck('id', 'name');

            $idsFor = fn (array $cases): array => $permissions
                ->only(collect($cases)->map(fn (BackedEnum $case) => $case->value)->all())
                ->values()
                ->all();

            $adminCases = $allCases
                ->reject(fn (BackedEnum $case) => SettingPermissions::Manage === $case)
                ->all();

            $orgAdminCases = [
                ...EventPermissions::cases(),
                ...UserPermissions::cases(),
                ...TicketTypePermissions::cases(),
                CheckInPermissions::Manage,
                OrderPermissions::View,
                DashboardPermissions::Access,
            ];

            Role::adminRole()->permissions()->sync($idsFor($adminCases));
            Role::organizationAdminRole()->permissions()->sync($idsFor($orgAdminCases));
        });
    }
}
