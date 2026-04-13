<?php

declare(strict_types=1);

use App\Enums\PreservedRoleList;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Policies\BasePolicy;
use App\Traits\HasAppUuid;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

arch('action classes are final')
    ->expect('App\Actions')
    ->toBeFinal();

arch('action classes have an execute method')
    ->expect('App\Actions')
    ->toHaveMethod('execute');

arch('controllers extend the base Controller')
    ->expect('App\Http\Controllers')
    ->toExtend(Controller::class);

arch('controllers do not use global helper functions')
    ->expect('App\Http\Controllers')
    ->not->toUse(['back', 'redirect', 'route', 'url']);

arch('value objects are readonly')
    ->expect('App\ValueObjects')
    ->toBeReadonly();

arch('DTOs are readonly')
    ->expect('App\DataTransferObjects')
    ->toBeReadonly();

arch('models use HasAppUuid')
    ->expect('App\Models')
    ->toUse(HasAppUuid::class)
    ->ignoring([
        Role::class,
        Permission::class,
        Setting::class,
    ]);

arch('policies extend BasePolicy')
    ->expect('App\Policies')
    ->toExtend(BasePolicy::class)
    ->ignoring(BasePolicy::class);

arch('no DB facade used in application code')
    ->expect('App')
    ->not->toUse(DB::class);

arch('actions and controllers do not dispatch events via static calls or Event facade')
    ->expect(['App\Actions', 'App\Http\Controllers'])
    ->not->toUse([
        Event::class,
        Dispatchable::class,
    ]);

arch('enums are backed enums')
    ->expect('App\Enums')
    ->toBeStringBackedEnum()
    ->ignoring([
        PreservedRoleList::class,
    ]);

arch('no throw_if or throw_unless helpers used')
    ->expect('App')
    ->not->toUse(['throw_if', 'throw_unless']);
