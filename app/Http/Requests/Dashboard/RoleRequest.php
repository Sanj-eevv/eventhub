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
        return new RoleData(
            name: $this->validated('name'),
            description: $this->validated('description'),
            permissions: $this->validated('permissions'),
        );
    }
}
