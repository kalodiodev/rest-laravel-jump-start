<?php

use Illuminate\Http\Request;

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

Route::post('register', 'Auth\Api\RegisterController@register');
Route::post('access', 'Auth\Api\AccessTokenController@access');
Route::post('email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('reset', 'Auth\ResetPasswordController@reset')->name('password.reset');

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('revoke', 'Auth\Api\AccessTokenController@revoke');
});
