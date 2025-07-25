<?php

use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::get('user' , [\App\Http\Controllers\AuthController::class , 'index'])->name('user.index');
// Route::middleware('auth:api')->group(function () {
//     Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
//     Route::post('/2fa/resend', [TwoFactorController::class, 'resend']);
// });

Route::group([

    // 'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('login' , [\App\Http\Controllers\AuthController::class , 'login']);
    Route::post('register' , [\App\Http\Controllers\AuthController::class , 'register']);
    Route::post('logout' , [\App\Http\Controllers\AuthController::class , 'logout']);
    Route::post('refresh' , [\App\Http\Controllers\AuthController::class , 'register']);
    
});

Route::apiResource('users', UserController::class);

Route::group([
    'middleware' => 'twofactor',
    'prefix' => 'auth'

], function(){
    Route::get('profile' , [\App\Http\Controllers\AuthController::class , 'profile']);
});