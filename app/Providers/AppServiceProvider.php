<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureCommands();
        $this->configureModels();
        $this->configureDates();
        $this->configureUrls();
        $this->configureVite();

        $this->configureRateLimit();
        $this->configurePasswordRules();
        Schema::defaultStringLength(191);
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            $this->app->environment('production'),
        );
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict();
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureUrls(): void
    {
        URL::forceScheme('https');
        VerifyEmail::createUrlUsing(fn(User $notifiable): string => URL::temporarySignedRoute(
            'auth.verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        ));
        Authenticate::redirectUsing(fn() => route('auth.login', absolute: false));
    }

    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }

    private function configurePasswordRules(): void
    {
        Password::defaults(function (): Password {
            if ( ! $this->app->environment('production')) {
                return Password::min(8);
            }
            return Password::min(8)
                -> mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
    }


    private function configureRateLimit(): void
    {
        $loginRateLimitedResponse = fn(Request $request): RedirectResponse => back()->withErrors([
            'email' => ['Too many login attempts. Please try again later.'],
        ])->withInput($request->except('password'));

        RateLimiter::for('login', fn(Request $request) => [
            Limit::perMinute(100)->by($request->ip())->response($loginRateLimitedResponse),
            Limit::perMinute(5)->by($request->input('email'))->response($loginRateLimitedResponse),
        ]);

    }
}
