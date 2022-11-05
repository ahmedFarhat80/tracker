<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| MUST BE AUTHENTICATED AS DRIVER FLUTTER APP
|--------------------------------------------------------------------------
*/

Route::group([ 'middleware' => ['auth:driver-api' , 'Localization'] ,'namespace' => 'Api'] , function(){
    Route::get('drivers', 'Flutter\Driver\ProfileController@info');
    Route::post('change-location', 'Flutter\Driver\ProfileController@updateLocation');
    Route::post('change-avatar', 'Flutter\Driver\ProfileController@changeAvatar');
    Route::post('destroy-avatar', 'Flutter\Driver\ProfileController@destroyAvatar');
    Route::post('fcm_token', 'Flutter\Driver\ProfileController@updateToken');
    Route::post('assign-order', 'Flutter\Driver\OrderController@update');
    Route::get('change-online', 'Flutter\Driver\ProfileController@online');
    Route::get('get-rejected-orders', 'Flutter\Driver\ProfileController@rejectedOrders');
    Route::get('get-accepted-orders', 'Flutter\Driver\ProfileController@acceptedOrders');
    Route::get('get-delivered-orders', 'Flutter\Driver\ProfileController@deliveredOrders');
    Route::get('get-pending-orders', 'Flutter\Driver\ProfileController@pendingOrders');

    Route::group(['prefix' => 'drivers'] , function(){
        Route::group(['prefix' => 'notifications'] , function(){
            Route::get('/', 'Flutter\Driver\NotificationController@index');
            Route::get('un-read', 'Flutter\Driver\NotificationController@unRead');
            Route::get('make-as-read/{id}', 'Flutter\Driver\NotificationController@makeRead');
            Route::get('destroy/{id}', 'Flutter\Driver\NotificationController@destroy');
        });

        // LOGOUT ROUTE
        Route::post('logout', 'Flutter\Driver\SigninController@logout');
    });

    
});