<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Services\Checkout\CheckoutService;
use App\Services\Checkout\Contracts\CheckoutServiceInterface;
use App\Services\Checkout\Contracts\StatusRepositoryInterface;
use App\Services\Checkout\Repositories\EloquentStatusRepository;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Override;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->bind(StatusRepositoryInterface::class, EloquentStatusRepository::class);
        $this->app->bind(CheckoutServiceInterface::class, CheckoutService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Gate::define('viewLogViewer', fn (User $user) => $user->hasRole('Administrador')
            ? Response::allow()
            : Response::deny(__('This action is unauthorized.')));
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    private function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
