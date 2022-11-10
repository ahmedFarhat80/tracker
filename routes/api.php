<?php

use App\Http\Controllers\Api\Shipping\NewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| GUEST ROUTES
|--------------------------------------------------------------------------
*/

Route::group(['namespace' => 'Api'], function () {
    /*
    |--------------------------------------------------------------------------
    | ADMINSTRATION AUTH ROUTES
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'admins'], function () {
        Route::post('register', 'Auth\Admin\RegisterController@register')->name('admin.register.api');
        Route::post('login', 'Auth\Admin\LoginController@login')->name('admin.login.api');
    });


    /*
    |--------------------------------------------------------------------------
    | RESTAURANT AUTH ROUTES
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'restaurants'], function () {
        Route::post('register', 'Auth\Restaurant\RegisterController@register')->name('restaurant.register.api');
        Route::post('login', 'Auth\Restaurant\LoginController@login')->name('restaurant.login.api');
    });

    /*
    |--------------------------------------------------------------------------
    | USER AUTH ROUTES
    |--------------------------------------------------------------------------
    */
    Route::post('register', 'Auth\User\RegisterController@register')->name('user.register.api');
    Route::post('login', 'Auth\User\LoginController@login')->name('user.login.api');

    /*
    |--------------------------------------------------------------------------
    | FLUTTER DRIVERS AUTH ROUTES
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'drivers'], function () {
        Route::post('check-phone', 'Flutter\Driver\CheckPhoneController@phone');
        Route::post('signin', 'Flutter\Driver\SigninController@login')->name('driver.login.api');
    });

    Route::post('generate-password', 'Auth\GeneratePasswordController@generatePassword');




    Route::group(['prefix' => 'new'], function () {
        Route::get('/get_Resturant_data/{id}', [NewController::class, 'get_Resturant_data']);
        Route::get('/get_driver_data/{id}', [NewController::class, 'get_driver_data']);
        Route::get('/new_get_Delivery_price', [NewController::class, 'new_get_Delivery_price']);
    });
}); // END GUEST ROUTES
