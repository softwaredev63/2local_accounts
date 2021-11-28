<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\UserController;

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

Route::middleware('client')->post('/import-old-users', [UserController::class, 'importOldUsers'])->name('import-old-users');

Route::middleware('client')->post('/auth/register', [UserController::class, 'register'])->name('api-register');
Route::middleware('client')->post('/auth/login', [UserController::class, 'login'])->name('api-login');
Route::middleware('auth:api')->post('/auth/validate-twofa', [UserController::class, 'validateTwofaCode'])->name('api-validate-twofa');

Route::group(['middleware' => ['client', 'auth:api']], function () {
    Route::post('/profile/update-profile', [UserController::class, 'updateProfile'])->name('api-update-profile');
    Route::get('/profile/get-profile', [UserController::class, 'getProfile'])->name('api-get-profile');
});

