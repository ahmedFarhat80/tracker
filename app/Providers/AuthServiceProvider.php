<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Order;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        /**
         * User Authentication
         * Check request user id is the same loged-in user id
         *
         * @return void
         */
        Gate::define('user-update-profile', function (User $user, Request $request) {
            return $user->id === (int)$request->id;
        });

        /**
         * Adminstration Authentication
         * Check request admin id is the same loged-in admin id
         *
         * @return void
         */
        Gate::define('admin-update-profile', function (Admin $admin, Request $request) {
            return $admin->id === (int)$request->id;
        });


        /**
         * Adminstration Authentication
         * Check request admin id is the same loged-in admin id
         *
         * @return void
         */
        Gate::define('restaurant-update-profile', function (Order $order, Request $request) {
            return $order->status === 'pending';
        });
        

        /**
         * Restaurant Authentication
         * Check status of order must != approved
         *
         * @return void
         */
        Gate::define('restaurant-update-order', function (Restaurant $restaurant, Request $request) {
            return $restaurant->id === (int)$request->id;
        });
        

        /**
         * User Authentication
         * Check driver specify logged-in user
         *
         * @return void
         */
        Gate::define('user-driver-check', function (User $user, $driverId) {
            $hasDriver = $user->drivers()->where('drivers.id', $driverId)->exists();
            return $hasDriver;
        });
    }
}
