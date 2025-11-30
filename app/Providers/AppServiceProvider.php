<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Hold;
use App\Observers\HoldObserver;

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
    { Hold::observe(HoldObserver::class);
       
    }
}
