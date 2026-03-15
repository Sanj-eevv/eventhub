<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard\Organizations;

use App\DataTransferObjects\OrganizationData;
use App\Enums\OrganizationStatus;
use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class OrganizationRequest extends FormRequest
{
    public function authorize(Request $request): bool
    {
        return $this->route('organization') ? $request->user()->can('update', Organization::class) : $request->user()->can('create', Organization::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string', 'max:5000'],
            'contact_address' => ['required', 'string', 'max:191'],
            'contact_email' => ['required', 'string', 'email', 'max:191', Rule::unique('organizations', 'contact_email')->ignore($this->route('organization'))],
            'status' => ['required', Rule::enum(OrganizationStatus::class)],
        ];
    }

    public function toDto(): OrganizationData
    {
        return OrganizationData::fromArray($this->validated());
    }
}
