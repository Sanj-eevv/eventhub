<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard\Roles;

use App\DataTransferObjects\RoleDto;
use Illuminate\Foundation\Http\FormRequest;

final class RoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string', 'max:191'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function toDto(): RoleDto
    {
        return RoleDto::fromArray($this->validated());
    }
}
