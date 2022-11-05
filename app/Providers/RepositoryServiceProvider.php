<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\DriverRepositoryInterface;
use App\Repositories\DriverRepository;
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DriverRepositoryInterface::class, DriverRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
