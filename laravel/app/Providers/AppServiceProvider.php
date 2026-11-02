<?php

namespace App\Providers;

use App\Support\Timezone;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleRequests\RequireLivewireHeaders;

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
        Event::listen(LocaleUpdated::class, fn (LocaleUpdated $event) => Timezone::apply($event->locale));

        Timezone::apply(app()->getLocale());

        Livewire::setUpdateRoute(function ($handle, $path) {
            return Route::post($path, $handle)
                ->middleware(['web', RequireLivewireHeaders::class])
                ->name('livewire.update')
                ->block(10, 10);
        });
    }
}
