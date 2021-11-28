<?php

use App\Http\Controllers\Wallet\WalletController;
use App\Http\Controllers\Wallet\UserPhrasesController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Nova\CustomNovaController;
use App\Http\Controllers\Wallet\BalanceController;
use App\Http\Controllers\Reset\ResetAccountController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware('cache.headers:private;no_store;max_age=0')->group(function () {
    Route::get('/', function () {
        return redirect(route('login'));
    });

    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::get('/change-pass', [CustomLoginController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('/change-pass', [CustomLoginController::class, 'doChangePassword'])->name('do-change-password');
    });

    Route::group(['middleware' => ['auth', 'verified', 'hasChangedPassword']], function () {
        Route::get('/set-phrases', [CustomLoginController::class, 'showSetPhrasesWizard'])->name('set-user-phrases');
        Route::get('/user/generate-phrases', [UserPhrasesController::class, 'getUserPhrases']);
        Route::put('/user/set-phrases', [UserPhrasesController::class, 'saveUserPhrases']);
    });

    Route::group(['middleware' => ['auth', 'verified', 'hasChangedPassword', 'secretPhraseIsSet']], function () {
        Route::get('/balance', [BalanceController::class, 'showBalancePage'])->name('balance');
        Route::get('/settings', [BalanceController::class, 'showSettingsPage'])->name('settings');
        Route::get('/user/private-key', [WalletController::class, 'getPrivateKey']);
        Route::get('/buy', [WalletController::class, 'buyWithSimplex'])->name('buy-page');
        Route::post('/send-crypto', [WalletController::class, 'sendCryptoToAddress']);
        Route::get('/get-gas-price', [WalletController::class, 'getGasPrice']);
        Route::post('/get-gas-limit', [WalletController::class, 'getGasLimit']);
    });

    Route::get('/download/nova/{fileName}', [CustomNovaController::class, 'downloadFileNova'])->name('download-file-nova');
});

Route::get('/elb-status', function () {
    return 'Everything is fine, nothing to see here.';
});

Route::get('/reset-infos', [ResetAccountController::class, 'showResetAccountPage'])->name('show-reset-account');
Route::post('/check-login-status', [ResetAccountController::class, 'checkLoginStatus']);
Route::post('/reset-user-info', [ResetAccountController::class, 'resetUserInfo']);
Route::get('/reset-infos/generate-phrases', [ResetAccountController::class, 'getUserPhrases']);
Route::put('/reset-infos/set-phrases', [ResetAccountController::class, 'saveUserPhrases']);

/* =========== Google Authentication =========== */
Route::get('/auth/google', [CustomLoginController::class, 'redirectToGoogle'])->name('redirect-to-google');
Route::get('/auth/google/callback', [CustomLoginController::class, 'handleGoogleCallback'])->name('handle-google-callback');
