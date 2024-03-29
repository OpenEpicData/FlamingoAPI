<?php

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

Route::group(['middleware' => 'api'], function () {
    Route::get('/', function () {
        return ([
            'gitHubSources' => 'https://github.com/OpenEpicData/FlamingoAPI'
        ]);
    });

    Route::group(['prefix' => 'auth'], function () {
        Route::post('verify-email', 'AuthController@VerifyEmail');
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');

        Route::group(['middleware' => 'auth:api'], function () {
            Route::post('logout', 'AuthController@logout');
            Route::get('user', 'AuthController@user');
        });
    });
});
