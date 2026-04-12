<?php

declare(strict_types=1);

namespace App\Rules;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

final class EndsOnDifferentCalendarDay implements DataAwareRule, ValidationRule
{
    private array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startsAt = $this->data['starts_at'] ?? null;
        if ( ! $startsAt) {
            return;
        }

        $startDate = CarbonImmutable::parse($startsAt)->toDateString();
        $endDate = CarbonImmutable::parse($value)->toDateString();
        if ($endDate <= $startDate) {
            $fail('The event must end on a different calendar day than it starts (minimum 1 day).');
        }
    }
}
