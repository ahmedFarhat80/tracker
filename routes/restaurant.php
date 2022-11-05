<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MUST BE AUTHENTICATED AS RESTAURANT
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['auth:restaurant-api', 'Localization'], 'namespace' => 'Api'], function () {

    Route::group(['prefix' => 'restaurants'], function () {
        /*
        |--------------------------------------------------------------------------
        | PROFILE Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'profile'], function () {
            Route::get('info', 'Restaurant\InfoController@info')->name('restaurant.profile.info');
            Route::get('orders-filter', 'Restaurant\InfoController@ordersFilter')->name('restaurant.profile.ordersFilter');
            Route::get('showByToken', 'Admin\RestaurantController@showByToken')->name('restaurant.profile.showByToken');
            Route::post('update', 'Admin\RestaurantController@update')->name('restaurant.profile.update');
            Route::post('change-password', 'Admin\RestaurantController@changePassword')->name('restaurant.profile.changePassword');
            Route::post('change-avatar', 'Admin\RestaurantController@changeAvatar')->name('restaurant.profile.changeAvatar');
            Route::post('destroy/avatar', 'Admin\RestaurantController@destroyAvatar')->name('restaurant.destroyAvatar');
        });

        /*
        |--------------------------------------------------------------------------
        | RESTAURANT DRIVERS Routes
        |--------------------------------------------------------------------------
        */
        Route::get('drivers', 'Restaurant\DriverController@index');
        Route::get('drivers/show/{id}', 'Restaurant\DriverController@show');
        Route::get('drivers/search', 'Restaurant\DriverController@search'); // added
    });

    /*
    |--------------------------------------------------------------------------
    | ORDER Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'Restaurant\OrderController@index');
        Route::get('active', 'Restaurant\OrderController@getActiveDrivers');
        Route::get('show/{id}', 'Restaurant\OrderController@show');
        Route::get('status/{id}', 'Restaurant\OrderController@status');
        Route::get('fetchNearestDrivers', 'Restaurant\OrderController@fetchNearestdrivers');
        Route::post('store', 'Restaurant\OrderController@store');
        Route::post('select-driver', 'Restaurant\OrderController@selectDriver');
        Route::post('update/{id}', 'Restaurant\OrderController@update');
        Route::post('destroy/{id}', 'Restaurant\OrderController@destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Wallet Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'wallet'], function () {
        Route::post('pay', 'Restaurant\WalletController@pay');
        // Route::get('show/{refNo}', 'Restaurant\WalletController@show');
        Route::get('callback', 'Restaurant\WalletController@callback');
        Route::get('callback_error', 'Restaurant\WalletController@callbackError');
    });
}); // END AUTHENTICATION AS USER
