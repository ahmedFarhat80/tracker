<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| MUST BE AUTHENTICATED AS ADMINSTRATION
|--------------------------------------------------------------------------
*/

Route::group([ 'middleware' => ['auth:admin-api' , 'Localization'] ,'namespace' => 'Api'] , function(){

    /*
    |--------------------------------------------------------------------------
    | ADMIN Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'admins'] , function(){
        Route::get('/' , 'Admin\AdminController@index')->name('admin.index');
        Route::post('store' , 'Admin\AdminController@store')->name('admin.store');
        Route::get('show/{id}' , 'Admin\AdminController@show')->name('admin.show');
        Route::get('status/{id}' , 'Admin\AdminController@status')->name('admin.status');
        Route::get('destroy/{id}', 'Admin\AdminController@destroy')->name('admin.destroy');
        Route::get('search', 'Admin\AdminController@search')->name('admin.search');

        /*
        |--------------------------------------------------------------------------
        | PROFILE Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'profile'] , function(){
            Route::post('update' , 'Admin\AdminController@update')->name('profile.update');
            Route::post('change-password' , 'Admin\AdminController@changePassword')->name('profile.changePassword');
            Route::post('change-avatar' , 'Admin\AdminController@changeAvatar')->name('profile.changeAvatar');
            Route::post('destroy/avatar' , 'Admin\AdminController@destroyAvatar')->name('admin.destroyAvatar');
        });


        /*
        |--------------------------------------------------------------------------
        | DRIVERS Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['prefix' => 'drivers'] , function(){
            Route::get('/', 'Admin\DriverController@index');
            Route::get('active', 'Admin\DriverController@getActiveDrivers');
            Route::get('show/{id}', 'Admin\DriverController@show');
            Route::get('status/{id}' , 'Admin\DriverController@status');
            Route::post('store', 'Admin\DriverController@store');
            Route::post('update/{id}', 'Admin\DriverController@update');
            Route::post('destroy/{id}', 'Admin\DriverController@destroy');
            Route::get('search', 'Admin\DriverController@search');
        });    
    });

    /*
    |--------------------------------------------------------------------------
    | USERS Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'users'] , function(){
        Route::get('/' , 'Admin\UserController@index')->name('user.index');
        Route::post('store' , 'Admin\UserController@store')->name('user.store');
        Route::post('update' , 'Admin\UserController@update')->name('user.update');
        Route::get('show/{id}' , 'Admin\UserController@show')->name('user.show');
        Route::get('status/{id}' , 'Admin\UserController@status')->name('user.status');
        Route::get('destroy/{id}', 'Admin\UserController@destroy')->name('user.destroy');
        Route::get('search', 'Admin\UserController@search')->name('user.search');
    });

    /*
    |--------------------------------------------------------------------------
    | Restaurants Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'restaurants'] , function(){
        Route::get('/' , 'Admin\RestaurantController@index')->name('restaurant.index');
        Route::post('store' , 'Admin\RestaurantController@store')->name('restaurant.store');
        Route::get('show/{id}' , 'Admin\RestaurantController@show')->name('restaurant.show');
        Route::get('status/{id}' , 'Admin\RestaurantController@status')->name('restaurant.status');
        Route::get('destroy/{id}', 'Admin\RestaurantController@destroy')->name('restaurant.destroy');
        Route::get('search', 'Admin\RestaurantController@search')->name('restaurant.search');
    });

    /*
    |--------------------------------------------------------------------------
    | QUOTES Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'quotes'] , function(){
        Route::get('/' , 'Admin\QuotesController@index')->name('quote.index');
        Route::post('store' , 'Admin\QuotesController@store')->name('quote.store');
        Route::get('show/{id}' , 'Admin\QuotesController@show')->name('quote.show');
        Route::post('update/{id}' , 'Admin\QuotesController@update')->name('quote.update');
        Route::get('status/{id}' , 'Admin\QuotesController@status')->name('quote.status');
        Route::get('destroy/{id}', 'Admin\QuotesController@destroy')->name('quote.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | CURRENCIES Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'currency'] , function(){
        Route::get('/' , 'Admin\CurrencyController@index')->name('currency.index');
        Route::post('store' , 'Admin\CurrencyController@store')->name('currency.store');
        Route::get('show/{id}' , 'Admin\CurrencyController@show')->name('currency.show');
        Route::post('update/{id}' , 'Admin\CurrencyController@update')->name('currency.update');
        Route::get('status/{id}' , 'Admin\CurrencyController@status')->name('currency.status');
        Route::get('destroy/{id}', 'Admin\CurrencyController@destroy')->name('currency.destroy');
        Route::get('search', 'Admin\CurrencyController@search')->name('currency.search');
    });

    /*
    |--------------------------------------------------------------------------
    | PAYMENT INFO SETTING Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'settings'] , function(){
        Route::get('payment-info' , 'Admin\PaymentInfoController@show');
        Route::post('payment-info' , 'Admin\PaymentInfoController@update');
    });

    /*
    |--------------------------------------------------------------------------
    | LOGOUT Route
    |--------------------------------------------------------------------------
    */
    Route::get('logout', 'Auth\Admin\LoginController@logout')->name('logout.api');
}); // END AUTHENTICATION AS ADMIN
