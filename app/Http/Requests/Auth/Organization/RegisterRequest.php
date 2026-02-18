<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string', 'max:5000'],
            'contact_address' => ['required', 'string', 'max:500'],
            'contact_email' => ['required', 'string', 'email', 'max:191', 'unique:organizations,contact_email'],
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'string', 'max:191', Password::defaults(), 'confirmed'],
        ];
    }
}
