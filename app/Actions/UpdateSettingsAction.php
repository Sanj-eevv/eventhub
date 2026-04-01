<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Setting;

final class UpdateSettingsAction
{
    public function execute(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
