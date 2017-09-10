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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('email_verification', [	'as'=>'api.emailVerification', 
									'uses' => 'UserVerificationController@sendEmailVerification']);
Route::get('email_verification_link', [ 'as'=> 'api.emailVerificationLink', 'uses' => 'UserVerificationController@verifyEmail']);
Route::post('sms_verification', ['as' => 'api.mobileVerification', 'uses' => 'UserVerificationController@sendMobileVerification']);
Route::post('mobile_otp_verification', ['as' => 'api.mobileVerificationLink', 'uses' => 'UserVerificationController@verifyMobile']);