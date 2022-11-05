<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use App\Observers\Admin\AdminObserver;
use App\Models\Admin;

use App\Observers\Admin\UserObserver;
use App\Models\User;

use App\Observers\Admin\DriverObserver;
use App\Models\Driver;


use App\Observers\Admin\RestaurantObserver;
use App\Models\Restaurant;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Admin::observe(AdminObserver::class);
        User::observe(UserObserver::class);
        Driver::observe(DriverObserver::class);
        Restaurant::observe(RestaurantObserver::class);
    }
}
