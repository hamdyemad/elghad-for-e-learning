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

    // Notifications Management
    Route::resource('notifications', \App\Http\Controllers\Dashboard\NotificationController::class)->except(['edit', 'update']);

    // Course Summaries Management (nested under courses)
    Route::prefix('courses/{course}')->name('courses.summaries.')->group(function () {
        Route::get('/summaries', [\App\Http\Controllers\Dashboard\CourseSummaryController::class, 'index'])->name('index');
        Route::get('/summaries/create', [\App\Http\Controllers\Dashboard\CourseSummaryController::class, 'create'])->name('create');
        Route::post('/summaries', [\App\Http\Controllers\Dashboard\CourseSummaryController::class, 'store'])->name('store');
        Route::get('/summaries/{summary}', [\App\Http\Controllers\Dashboard\CourseSummaryController::class, 'show'])->name('show');
        Route::get('/summaries/{summary}/edit', [\App\Http\Controllers\Dashboard\CourseSummaryController::class, 'edit'])->name('edit');
        Route::put('/summaries/{summary}', [\App\Http\Controllers\Dashboard\CourseSummaryController::class, 'update'])->name('update');
        Route::delete('/summaries/{summary}', [\App\Http\Controllers\Dashboard\CourseSummaryController::class, 'destroy'])->name('destroy');
    });

    // Exams Management (all exams)
    Route::get('exams', [\App\Http\Controllers\Dashboard\ExamController::class, 'allExams'])->name('exams.all');

    // Live Streams Management (all)
    Route::get('live-streams', [\App\Http\Controllers\Dashboard\LiveStreamController::class, 'allLiveStreams'])->name('live-streams.all');

    // Course Exams Management (nested under courses)
    Route::prefix('courses/{course}')->name('courses.exams.')->group(function () {
        Route::get('/exams', [\App\Http\Controllers\Dashboard\ExamController::class, 'index'])->name('index');
        Route::get('/exams/create', [\App\Http\Controllers\Dashboard\ExamController::class, 'create'])->name('create');
        Route::post('/exams', [\App\Http\Controllers\Dashboard\ExamController::class, 'store'])->name('store');
        Route::get('/exams/{exam}', [\App\Http\Controllers\Dashboard\ExamController::class, 'show'])->name('show');
        Route::get('/exams/{exam}/edit', [\App\Http\Controllers\Dashboard\ExamController::class, 'edit'])->name('edit');
        Route::put('/exams/{exam}', [\App\Http\Controllers\Dashboard\ExamController::class, 'update'])->name('update');
        Route::delete('/exams/{exam}', [\App\Http\Controllers\Dashboard\ExamController::class, 'destroy'])->name('destroy');
    });

    // Course Live Streams Management (nested under courses)
    Route::prefix('courses/{course}')->name('courses.live-streams.')->group(function () {
        Route::get('/live-streams', [\App\Http\Controllers\Dashboard\LiveStreamController::class, 'index'])->name('index');
        Route::get('/live-streams/create', [\App\Http\Controllers\Dashboard\LiveStreamController::class, 'create'])->name('create');
        Route::post('/live-streams', [\App\Http\Controllers\Dashboard\LiveStreamController::class, 'store'])->name('store');
        Route::get('/live-streams/{liveStream}', [\App\Http\Controllers\Dashboard\LiveStreamController::class, 'show'])->name('show');
        Route::get('/live-streams/{liveStream}/edit', [\App\Http\Controllers\Dashboard\LiveStreamController::class, 'edit'])->name('edit');
        Route::put('/live-streams/{liveStream}', [\App\Http\Controllers\Dashboard\LiveStreamController::class, 'update'])->name('update');
        Route::delete('/live-streams/{liveStream}', [\App\Http\Controllers\Dashboard\LiveStreamController::class, 'destroy'])->name('destroy');
    });

    // Students Management
    Route::resource('students', \App\Http\Controllers\Dashboard\StudentController::class);
    Route::post('students/{id}/upgrade-to-instructor', [\App\Http\Controllers\Dashboard\StudentController::class, 'upgradeToInstructor'])->name('students.upgrade-to-instructor')->middleware('role:admin');

    // Instructors Management
    Route::resource('instructors', \App\Http\Controllers\Dashboard\InstructorController::class);
});

// API Test Page
Route::get('/test-api', function () {
    return view('test-api');
});
Route::post('/test-api', function (\Illuminate\Http\Request $request) {
    $url = $request->input('url', 'http://160.19.103.122:40120/YusorOnline/api/OnlinePaymentServices/Signin');
    $body = $request->input('body', json_encode([
        "userId" => 100589,
        "pin" => "U3f@Zh",
        "providerId" => 7070,
        "authUserType" => 0
    ], JSON_UNESCAPED_UNICODE));

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return response()->json([
        'url' => $url,
        'http_code' => $httpCode,
        'error' => $error ?: null,
        'response' => json_decode($response, true) ?? $response,
    ]);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

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
