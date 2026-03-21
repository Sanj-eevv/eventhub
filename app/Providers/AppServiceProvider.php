<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\EventCancelled;
use App\Events\OrderCompleted;
use App\Events\OrganizationApproved;
use App\Events\OrganizationRejected;
use App\Listeners\GenerateTicketQrCodes;
use App\Listeners\NotifyEventTicketHolders;
use App\Listeners\SendOrderConfirmationNotification;
use App\Listeners\SendOrganizationApprovedMail;
use App\Listeners\SendOrganizationRejectedMail;
use App\Listeners\VoidEventTickets;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\Policies\OrderPolicy;
use App\Policies\TicketPolicy;
use App\Policies\TicketTypePolicy;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        $this->configurePolicies();
        $this->configureEvents();
        $this->configureCommands();
        $this->configureModels();
        $this->configureDates();
        $this->configureUrls();
        $this->configureVite();

        $this->configureRateLimit();
        $this->configurePasswordRules();
        $this->configureDatabase();
        JsonResource::withoutWrapping();
    }

    private function configurePolicies(): void
    {
        Gate::policy(TicketType::class, TicketTypePolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
    }

    private function configureEvents(): void
    {
        Event::listen(OrganizationApproved::class, SendOrganizationApprovedMail::class);
        Event::listen(OrganizationRejected::class, SendOrganizationRejectedMail::class);

        Event::listen(OrderCompleted::class, GenerateTicketQrCodes::class);
        Event::listen(OrderCompleted::class, SendOrderConfirmationNotification::class);

        Event::listen(EventCancelled::class, VoidEventTickets::class);
        Event::listen(EventCancelled::class, NotifyEventTicketHolders::class);
    }

    private function configureDatabase(): void
    {

        Schema::defaultStringLength(191);
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict();
        Model::preventLazyLoading( ! app()->isProduction());
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureUrls(): void
    {
        URL::forceScheme('https');
        ResetPassword::createUrlUsing(fn (User $user, string $token) => URL::route('auth.password.reset', ['token' => $token, 'email' => $user->email]));
        VerifyEmail::createUrlUsing(fn (User $notifiable): string => URL::temporarySignedRoute(
            'auth.verification.verify',
            CarbonImmutable::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        ));
        Authenticate::redirectUsing(fn () => URL::route('auth.login', absolute: false));
    }

    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }

    private function configurePasswordRules(): void
    {
        Password::defaults(function (): Password {
            if ( ! app()->isProduction()) {
                return Password::min(8);
            }

            return Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
    }

    private function configureRateLimit(): void
    {
        $loginRateLimitedResponse = fn (Request $request): RedirectResponse => app(Redirector::class)->back()->withErrors([
            'email' => ['Too many login attempts. Please try again later.'],
        ])->withInput($request->except('password'));
        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(100)->by($request->ip())->response($loginRateLimitedResponse),
            Limit::perMinute(5)->by($request->input('email'))->response($loginRateLimitedResponse),
        ]);
        RateLimiter::for('password-reset-request', fn (Request $request) => [
            Limit::perHour(10)->by($request->ip()),
            Limit::perMinute(3)->by($request->input('email')),
        ]);
        RateLimiter::for('password-reset', fn (Request $request) => [
            Limit::perHour(5)->by($request->ip()),
            Limit::perHour(3)->by($request->input('email')),
        ]);

    }
}
