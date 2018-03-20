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
Route::post('email', 'Auth\Api\ForgotPasswordController@sendResetLinkEmail');
Route::post('reset', 'Auth\Api\ResetPasswordController@reset')->name('password.reset');
Route::post('refresh', 'Auth\Api\AccessTokenController@refresh');

Route::group(['middleware' => 'auth:api'], function() {
    Route::delete('delete', 'Auth\Api\RegisterController@destroy');
    Route::post('revoke', 'Auth\Api\AccessTokenController@revoke');
});
