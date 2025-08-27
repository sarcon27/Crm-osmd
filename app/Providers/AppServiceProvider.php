<?php

namespace App\Providers;

use App\Events\ApartmentCreatedEvent;
use App\Listeners\ProcessApartmentCreateAccount;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            ApartmentCreatedEvent::class,
            [ProcessApartmentCreateAccount::class, 'handle']
        );

    }
}
