<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings.
     * Format: Interface::class => Implementation::class
     *
     * @var array<string, string>
     */
    protected array $repositories = [
        \App\Repositories\Contracts\CourtRepositoryInterface::class => \App\Repositories\Eloquent\CourtRepository::class,
        \App\Repositories\Contracts\UserRepositoryInterface::class => \App\Repositories\Eloquent\UserRepository::class,
        \App\Repositories\Contracts\ReservationRepositoryInterface::class => \App\Repositories\Eloquent\ReservationRepository::class,
        \App\Repositories\Contracts\PaymentRepositoryInterface::class => \App\Repositories\Eloquent\PaymentRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
