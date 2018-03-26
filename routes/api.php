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

Route::post('register', 'Auth\Api\RegisterController@register')->name('api.register');
Route::post('access', 'Auth\Api\AccessTokenController@access')->name('api.access');
Route::post('email', 'Auth\Api\ForgotPasswordController@sendResetLinkEmail')->name('api.password.forgot');
Route::post('reset', 'Auth\Api\ResetPasswordController@reset')->name('api.password.reset');
Route::post('refresh', 'Auth\Api\AccessTokenController@refresh')->name('api.token.refresh');
Route::get('verify/{token}', 'Auth\Api\EmailVerificationController@verify')->name('api.email.verify');

Route::group(['middleware' => 'auth:api'], function() {
    Route::delete('delete', 'Auth\Api\RegisterController@destroy')->name('api.deregister');
    Route::post('revoke', 'Auth\Api\AccessTokenController@revoke')->name('api.token.revoke');
});
