<?php

use Illuminate\Support\Facades\Auth;
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

Auth::routes();

// Dashboard Routes
Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {
    Route::get('/', [\App\Http\Controllers\LexaAdmin::class, 'root'])->name('index');
    Route::resource('categories', \App\Http\Controllers\Dashboard\CategoryController::class);
    Route::resource('courses', \App\Http\Controllers\Dashboard\CourseController::class);
    Route::resource('packages', \App\Http\Controllers\Dashboard\PackageController::class);
    Route::get('lessons/reorder', [\App\Http\Controllers\Dashboard\LessonController::class, 'reorderForm'])->name('lessons.reorder.form');
    Route::post('lessons/reorder', [\App\Http\Controllers\Dashboard\LessonController::class, 'reorder'])->name('lessons.reorder');
    Route::resource('lessons', \App\Http\Controllers\Dashboard\LessonController::class)->except(['show']);
    Route::get('lessons/{id}', [\App\Http\Controllers\Dashboard\LessonController::class, 'show'])->name('lessons.show');
    Route::get('settings', [\App\Http\Controllers\Dashboard\SiteSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [\App\Http\Controllers\Dashboard\SiteSettingController::class, 'update'])->name('settings.update');
    Route::delete('lessons/{id}', [\App\Http\Controllers\Dashboard\LessonController::class, 'destroy'])->name('lessons.destroy');

    // Students Management
    Route::resource('students', \App\Http\Controllers\Dashboard\StudentController::class);
    Route::post('students/{id}/upgrade-to-instructor', [\App\Http\Controllers\Dashboard\StudentController::class, 'upgradeToInstructor'])->name('students.upgrade-to-instructor')->middleware('role:admin');

    // Instructors Management
    Route::resource('instructors', \App\Http\Controllers\Dashboard\InstructorController::class);
});

// Wallet Routes
Route::prefix('dashboard/wallet')->middleware('auth')->name('wallet.')->group(function () {
    // Self routes (user managing own wallet)
    Route::get('/', [\App\Http\Controllers\Dashboard\WalletController::class, 'index'])->name('index');
    Route::get('/deposit', [\App\Http\Controllers\Dashboard\WalletController::class, 'createDeposit'])->name('deposit.form');
    Route::post('/deposit', [\App\Http\Controllers\Dashboard\WalletController::class, 'storeDeposit'])->name('deposit');
    Route::get('/withdraw', [\App\Http\Controllers\Dashboard\WalletController::class, 'createWithdraw'])->name('withdraw.form');
    Route::post('/withdraw', [\App\Http\Controllers\Dashboard\WalletController::class, 'storeWithdraw'])->name('withdraw');

    // Admin routes for managing other users' wallets
    Route::prefix('user/{user}')->middleware('role:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\WalletController::class, 'show'])->name('user.show');
        Route::get('/deposit', [\App\Http\Controllers\Dashboard\WalletController::class, 'createDeposit'])->name('user.deposit.form');
        Route::post('/deposit', [\App\Http\Controllers\Dashboard\WalletController::class, 'storeDeposit'])->name('user.deposit');
        Route::get('/withdraw', [\App\Http\Controllers\Dashboard\WalletController::class, 'createWithdraw'])->name('user.withdraw.form');
        Route::post('/withdraw', [\App\Http\Controllers\Dashboard\WalletController::class, 'storeWithdraw'])->name('user.withdraw');
    });
});

// Render perticular view file by foldername and filename and all passed in only one controller at a time
Route::get('{folder}/{file}', 'LexaAdmin@index');

// when render first time project redirect
Route::get('/home', function () {
    return redirect('dashboard');
});

Route::get('/keep-live', "LexaAdmin@live");

// when render first time project redirect
Route::get('/', function () {
    return redirect('login');
});
