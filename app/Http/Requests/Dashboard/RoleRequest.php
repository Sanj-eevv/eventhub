<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use App\DataTransferObjects\RoleData;
use Illuminate\Foundation\Http\FormRequest;

final class RoleRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string', 'max:191'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function toDto(): RoleData
    {
        $rawPermissions = $this->validated('permissions');

        return new RoleData(
            name: $this->string('name')->value(),
            description: $this->string('description')->value(),
            permissions: is_array($rawPermissions) ? collect($rawPermissions)->map(fn (mixed $value): int => is_numeric($value) ? (int) $value : 0)->all() : null,
        );
    }
}
