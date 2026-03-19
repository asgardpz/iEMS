<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\QRCodeLoginController;
use App\Http\Controllers\RealtimeController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\transactionsController;
use App\Http\Controllers\maintenanceController;
use App\Http\Controllers\workorderController;
use App\Http\Controllers\memberController;
use App\Http\Controllers\permissionController;
use App\Http\Controllers\accountController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

//Route::view('dashboard', 'dashboard')
//   ->middleware(['auth', 'verified'])
//   ->name('dashboard');

Route::get('/dashboard', [dashboardController::class, 'show'])
   ->middleware(['auth', 'verified'])
   ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::get('/report', [ReportController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('report');

Route::get('/device', [DeviceController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('device');

Route::get('/realtime', [RealtimeController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('realtime');

Route::get('/generate/{device_id}', [QRCodeLoginController::class, 'generate'])->name('qrcode.generate');
Route::get('/inuse/{session_id}/{device_id}', [QRCodeLoginController::class, 'inuse'])
    ->middleware(['auth'])
    ->name('qrcode.inuse');

// QRCODE登入
Route::get('/send/{device_id}', [QRCodeLoginController::class, 'send'])->name('qrcode.send');
Route::get('/recive/{device_id}', [QRCodeLoginController::class, 'recive'])->name('qrcode.recive');
Route::get('/use/{device_id}', [QRCodeLoginController::class, 'useDevice'])->name('qrcode.use');
Route::get('/wait-device/{device_id}', [QRCodeLoginController::class, 'waitDevice'])
    ->name('qrcode.waitDevice');

// 2期    
Route::get('/transactions', [transactionsController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('transactions');

Route::get('/maintenance', [maintenanceController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('maintenance');

Route::get('/workorders', [workorderController::class, 'index'])->name('workorders.index');
Route::post('/workorders', [workorderController::class, 'store'])->name('workorders.store');
// 顯示新增工單表單 
Route::get('/workorders/create', [workorderController::class, 'create'])->name('workorders.create');
Route::post('/workorders/create', [workorderController::class, 'save'])->name('workorders.save');

Route::get('/member', [memberController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('member');

// 儲存會員資料 (Save 按鈕)
Route::post('/member/store', [memberController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('store');

Route::get('/permission', [permissionController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('permission');

// 儲存權限資料 (Save 按鈕)
Route::post('/permission/store', [permissionController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('permission.store');    

Route::get('/account', [accountController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('account');

// 儲存會員資料 (Save 按鈕)
Route::post('/account/store', [accountController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('account.store');

Route::get('/account/delete/{id}', [accountController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('account.delete');
