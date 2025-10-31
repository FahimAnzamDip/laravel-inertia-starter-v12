<?php

use App\Http\Controllers\Admin\ActivityLogsController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Settings\CurrenciesController;
use App\Http\Controllers\Admin\Settings\PaymentMethodsController;
use App\Http\Controllers\Admin\Settings\SettingsController;
use App\Http\Controllers\Admin\Users\RolesController;
use App\Http\Controllers\Admin\Users\UsersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

// Admin
Route::group(['prefix' => 'admin'], function () {
    Route::middleware('auth')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])
            ->name('dashboard');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Uploads
        Route::post('/filepond/upload', [UploadsController::class, 'filepondUpload'])
            ->name('filepond.upload');
        Route::delete('/filepond/revert/{filename}', [UploadsController::class, 'filepondRevert'])
            ->name('filepond.revert');
        Route::delete('/filepond/remove/{filename}', [UploadsController::class, 'filepondRemove'])
            ->name('filepond.remove');

        // Users
        Route::resource('users', UsersController::class)->except('show');

        /* --------------------------------------------------------------------------------------- */

        // Roles
        Route::resource('roles', RolesController::class)->except('show');

        // Currencies
        Route::resource('currencies', CurrenciesController::class)->except('show', 'create');

        // Payment Methods
        Route::resource('payment-methods', PaymentMethodsController::class)
            ->except('show', 'create');

        // Activity Logs
        Route::delete('/activity-logs/multiple', [ActivityLogsController::class, 'destroyMultiple'])
            ->name('activity-logs.destroy.multiple');
        Route::resource('/activity-logs', ActivityLogsController::class)
            ->only(['index', 'destroy']);

        // Settings
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings/{setting}', [SettingsController::class, 'update'])
            ->name('settings.update');
    });
});
