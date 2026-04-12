<?php

declare(strict_types=1);

use App\Http\Controllers\Controller;
use App\Policies\BasePolicy;
use App\Traits\HasAppUuid;

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
        'App\Models\Role',
        'App\Models\Permission',
        'App\Models\Setting',
    ]);

arch('policies extend BasePolicy')
    ->expect('App\Policies')
    ->toExtend(BasePolicy::class)
    ->ignoring(BasePolicy::class);

arch('no DB facade used in application code')
    ->expect('App')
    ->not->toUse('Illuminate\Support\Facades\DB');

arch('actions and controllers do not dispatch events via static calls or Event facade')
    ->expect(['App\Actions', 'App\Http\Controllers'])
    ->not->toUse([
        'Illuminate\Support\Facades\Event',
        'Illuminate\Foundation\Events\Dispatchable',
    ]);

arch('enums are backed enums')
    ->expect('App\Enums')
    ->toBeStringBackedEnum()
    ->ignoring([
        'App\Enums\PreservedRoleList',
    ]);
