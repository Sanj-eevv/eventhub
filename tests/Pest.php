<?php

declare(strict_types=1);

use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        $this->seed([RoleSeeder::class, PermissionSeeder::class, SettingSeeder::class]);
    })
    ->in('Feature');

pest()
    ->extend(TestCase::class)
    ->in('Unit');

expect()->extend('toBeOne', fn () => $this->toBe(1));
