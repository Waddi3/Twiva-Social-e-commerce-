<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\Http\Controllers\API\V1\UserRedeemController;
use Modules\Business\Http\Controllers\API\V2\Influencer\UserController;
use Modules\User\Http\Controllers\API\V2\ApiUserController;
use Modules\User\Http\Controllers\API\V3\AuthController;

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
Route::domain(config('app.api_url'))->prefix('v1')->group(function () {
    Route::post('email/status', [ApiUserController::class, 'checkIfEmailExists'])->name('api-check-if-email-exists');

    Route::middleware('auth:api')->group(function () {
        Route::get('user/login', [ApiUserController::class, 'RedirectLogin'])->name('login')->withoutMiddleware('auth:api');
    });
});
Route::domain(config('app.api_url'))->prefix('v1')->group(function () {
    Route::post('user/login', [AuthController::class, 'login']);

    Route::post('user/forget-password', [AuthController::class, 'forgetPassword']);
    Route::post('user/verify-forget-password-otp', [AuthController::class, 'verifyForgetPasswordOtp']);
    Route::post('user/reset-password', [AuthController::class, 'resetPassword']);

    Route::post('user/resend-otp-for-register-verification', [AuthController::class, 'resendOtpForRegisterVerification']);
    Route::post('user/verify-registration-otp', [AuthController::class, 'verifyRegistrationOTP']);

    Route::post('user/send-otp-to-verify-phone-or-email', [AuthController::class, 'sendOTPToVerifyPhoneOrEmail'])->middleware('auth:api');
    Route::post('user/verify-email-phone-otp', [AuthController::class, 'verifyEmailPhoneOTP'])->middleware('auth:api');
    Route::post('user/delete-account', [AuthController::class, 'deleteAccount'])->middleware('auth:api');


    Route::post('user/logout', [AuthController::class, 'logout'])->middleware('auth:api');
});


Route::domain(config('app.api_url'))->prefix('v1')->middleware('auth:api')->group(function () {
    Route::get('redeem', [UserRedeemController::class, 'redeemReferral'])->name('api.user.redeem.referral');
});
