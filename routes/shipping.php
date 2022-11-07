<?php

use App\Http\Controllers\Api\Shipping\FareController;
use App\Http\Controllers\Api\Shipping\NewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MUST BE AUTHENTICATED AS SHIPPING
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['auth:user-api', 'Localization'], 'namespace' => 'Api'], function () {

    Route::group(['prefix' => 'users'], function () {
        /*
        |--------------------------------------------------------------------------
        | PROFILE Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'profile'], function () {
            Route::get('info', 'Shipping\InfoController@info')->name('user.profile.info');
            Route::get('orders-filter', 'Shipping\InfoController@ordersFilter')->name('user.profile.ordersFilter');
            Route::get('transaction-info', 'Shipping\InfoController@transactionInfo')->name('user.profile.transaction-info');
            Route::get('foreign-transaction-info', 'Shipping\InfoController@foreignTransactionInfo')->name('user.profile.foreign-transaction-info');
            Route::get('showByToken', 'Admin\UserController@showByToken')->name('user.profile.showByToken');
            Route::post('update', 'Admin\UserController@update')->name('user.profile.update');
            Route::post('change-password', 'Admin\UserController@changePassword')->name('user.profile.changePassword');
            Route::post('change-bank-info', 'Admin\UserController@changeBankInfo')->name('user.profile.changeBankInfo');
            Route::post('change-avatar', 'Admin\UserController@changeAvatar')->name('user.profile.changeAvatar');
            Route::post('destroy/avatar', 'Admin\UserController@destroyAvatar')->name('user.destroyAvatar');
        });


        /*
        |--------------------------------------------------------------------------
        | DRIVERS Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'drivers'], function () {
            Route::get('/', 'Admin\DriverController@index');
            Route::get('active', 'Admin\DriverController@getActiveDrivers');
            Route::get('show/{id}', 'Admin\DriverController@show');
            Route::get('status/{id}', 'Admin\DriverController@status');
            Route::post('store', 'Admin\DriverController@store');
            Route::post('update/{id}', 'Admin\DriverController@update');
            Route::post('destroy/{id}', 'Admin\DriverController@destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Restaurant Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'restaurants'], function () {
            Route::get('/', 'Shipping\RestaurantController@index');
            Route::post('store', 'Shipping\RestaurantController@store');
            Route::post('update/{id}', 'Shipping\RestaurantController@update');
            Route::get('status/{id}', 'Shipping\RestaurantController@status');
            Route::get('show/{id}', 'Shipping\RestaurantController@show');
            Route::post('destroy/{id}', 'Shipping\RestaurantController@destroy');
            Route::get('search', 'Shipping\RestaurantController@search');
        });

        /*
        |--------------------------------------------------------------------------
        | ORDER Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'orders'], function () {
            Route::get('/', 'Shipping\OrderController@index');
            // Route::get('active', 'Shipping\OrderController@getActiveDrivers');
            Route::get('show/{id}', 'Shipping\OrderController@show');
            // Route::get('status/{id}' , 'Shipping\OrderController@status');
            // Route::get('fetchNearestDrivers' , 'Shipping\OrderController@fetchNearestdrivers');
            Route::post('store', 'Shipping\OrderController@store');
            // Route::post('select-driver', 'Shipping\OrderController@selectDriver');
            Route::post('update/{id}', 'Shipping\OrderController@update');
            Route::post('destroy/{id}', 'Shipping\OrderController@destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | DRIVERS Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'drivers'], function () {
            Route::get('search', 'Shipping\DriverController@search');
        });

        /*
        |--------------------------------------------------------------------------
        | QUOTES Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'quotes'], function () {
            Route::get('/', 'Shipping\QuoteController@index');
        });

        /*
        |--------------------------------------------------------------------------
        | Transaction Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'transactions'], function () {
            Route::post('pay', 'Shipping\TransactionController@pay');
            Route::get('show/{refNo}', 'Shipping\TransactionController@show');
            Route::get('callback_success', 'Shipping\TransactionController@callback');
            Route::get('callback_error', 'Shipping\TransactionController@callbackError');
        });

        /*
        |--------------------------------------------------------------------------
        | Settings Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'settings'], function () {
            Route::get('/', 'Shipping\SettingController@index');
            Route::post('store', 'Shipping\SettingController@store');
            Route::post('update/{id}', 'Shipping\SettingController@update');
            Route::get('show/{id}', 'Shipping\SettingController@show');
            Route::post('destroy/{id}', 'Shipping\SettingController@destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Fare Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'fares'], function () {
            Route::post('store', [FareController::class, 'store']);
            Route::post('update/{id}', 'Shipping\FareController@update');
        });

        /*
        |--------------------------------------------------------------------------
        | Foreign transaction Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'foreign_transactions'], function () {
            Route::get('/', 'Shipping\ForeignTransactionController@index');
            Route::post('pay', 'Shipping\ForeignTransactionController@pay');
            Route::get('callback_success', 'Shipping\ForeignTransactionController@callback');
            Route::get('callback_error', 'Shipping\ForeignTransactionController@callbackError');
        });


    });
}); // END AUTHENTICATION AS USER
