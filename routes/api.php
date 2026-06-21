<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public Auth Routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

    // Email verification
    Route::post('/email/verify', [\App\Http\Controllers\Api\AuthController::class, 'verifyEmail']);
    Route::post('/email/resend', [\App\Http\Controllers\Api\AuthController::class, 'resendVerification']);
    Route::get('/email/check/{email}', [\App\Http\Controllers\Api\AuthController::class, 'checkVerification']);

    // Password reset
    Route::post('/password/forgot', [\App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);
    Route::post('/password/reset', [\App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
});

// Tlync Webhook
Route::post('/payment/tlync/webhook', [\App\Http\Controllers\Api\WalletController::class, 'tlyncWebhook'])->name('payment.tlync.webhook');

// Protected Auth Routes (require authentication)
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
});

// Wallet Routes (require authentication)
Route::middleware('auth:sanctum')->prefix('wallet')->name('api.wallet.')->group(function () {
    // Get wallet balance
    Route::get('/', [\App\Http\Controllers\Api\WalletController::class, 'index'])->name('index');

    // Get authenticated user's transactions
    Route::get('/transactions', [\App\Http\Controllers\Api\WalletController::class, 'transactions'])->name('transactions');

    // Deposit and withdraw
    Route::post('/deposit', [\App\Http\Controllers\Api\WalletController::class, 'deposit'])->name('deposit');
    Route::post('/withdraw', [\App\Http\Controllers\Api\WalletController::class, 'withdraw'])->name('withdraw');

    // Tlync Payment
    Route::post('/topup/initiate', [\App\Http\Controllers\Api\WalletController::class, 'initiateTopUp'])->name('topup.initiate');
});



// Public Categories API Routes
Route::prefix('categories')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('/parents', [\App\Http\Controllers\Api\CategoryController::class, 'parents']);
    Route::get('/tree', [\App\Http\Controllers\Api\CategoryController::class, 'tree']);
    Route::get('/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'show']);
});

// User Courses API Routes (require authentication)
Route::middleware('auth:sanctum')->prefix('courses')->name('api.courses.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\CourseController::class, 'index'])->name('index');
    Route::get('/free', [\App\Http\Controllers\Api\CourseController::class, 'free'])->name('free');
    Route::get('/{id}', [\App\Http\Controllers\Api\CourseController::class, 'show'])->name('show');
    Route::get('/{id}/summaries', [\App\Http\Controllers\Api\CourseSummaryController::class, 'index'])->name('summaries');
    Route::get('/{id}/exams', [\App\Http\Controllers\Api\ExamController::class, 'index'])->name('exams');
    Route::get('/{id}/live', [\App\Http\Controllers\Api\LiveStreamController::class, 'show'])->name('live');
});

// User Exams API Routes (require authentication)
Route::middleware('auth:sanctum')->prefix('exams')->name('api.exams.')->group(function () {
    Route::get('/{id}', [\App\Http\Controllers\Api\ExamController::class, 'show'])->name('show');
});

// User Packages API Routes (require authentication)
Route::middleware('auth:sanctum')->prefix('packages')->name('api.packages.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\PackageController::class, 'index'])->name('index');
    Route::get('/{id}', [\App\Http\Controllers\Api\PackageController::class, 'show'])->name('show');
});

// User Lessons API Routes (require authentication)
Route::middleware('auth:sanctum')->prefix('lessons')->name('api.lessons.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\LessonController::class, 'index'])->name('index');
    Route::get('/free', [\App\Http\Controllers\Api\LessonController::class, 'free'])->name('free');
    Route::get('/course/{courseId}', [\App\Http\Controllers\Api\LessonController::class, 'byCourse'])->name('by_course');
    Route::get('/{id}', [\App\Http\Controllers\Api\LessonController::class, 'show'])->name('show');
});

// Subscription Routes (require authentication)
// POST /api/subscribe?type=course|package&id={id}
// DELETE /api/subscribe?type=course|package&id={id}
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/subscribe', [\App\Http\Controllers\Api\SubscriptionController::class, 'subscribe'])->name('api.subscribe');
    Route::delete('/subscribe', [\App\Http\Controllers\Api\SubscriptionController::class, 'unsubscribe'])->name('api.subscribe.delete');
    Route::get('/subscriptions', [\App\Http\Controllers\Api\SubscriptionController::class, 'index'])->name('api.subscriptions.index');

    // User Notifications
    Route::prefix('notifications')->name('api.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::post('/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])->name('read');
    });
});

// Admin Dashboard API Routes (Require authentication + admin role)
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('dashboard')->group(function () {
    // Categories CRUD (Admin only)
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\CategoryController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Dashboard\Api\CategoryController::class, 'store'])->name('store');

        // Additional admin endpoints (must come before {id} routes)
        Route::get('/parents/list', [\App\Http\Controllers\Dashboard\Api\CategoryController::class, 'parents'])->name('parents');
        Route::get('/tree', [\App\Http\Controllers\Dashboard\Api\CategoryController::class, 'tree'])->name('tree');
        Route::post('/reorder', [\App\Http\Controllers\Dashboard\Api\CategoryController::class, 'reorder'])->name('reorder');

        // Parameterized routes (placed after specific routes)
        Route::get('/{id}', [\App\Http\Controllers\Dashboard\Api\CategoryController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Dashboard\Api\CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Dashboard\Api\CategoryController::class, 'destroy'])->name('destroy');
    });

    // Students CRUD (Admin only)
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\StudentController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Dashboard\Api\StudentController::class, 'store'])->name('store');

        // Additional endpoints (must come before {id} routes)
        Route::get('/active', [\App\Http\Controllers\Dashboard\Api\StudentController::class, 'active'])->name('active');
        Route::get('/inactive', [\App\Http\Controllers\Dashboard\Api\StudentController::class, 'inactive'])->name('inactive');

        // Parameterized routes (placed after specific routes)
        Route::get('/{id}', [\App\Http\Controllers\Dashboard\Api\StudentController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Dashboard\Api\StudentController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Dashboard\Api\StudentController::class, 'destroy'])->name('destroy');

        // Upgrade endpoint
        Route::post('/{id}/upgrade-to-instructor', [\App\Http\Controllers\Dashboard\Api\StudentController::class, 'upgradeToInstructor'])->name('upgrade_to_instructor');
    });

    // Courses CRUD (Admin only)
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\CourseController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Dashboard\Api\CourseController::class, 'store'])->name('store');

        // Additional endpoints (must come before {id} routes)
        Route::get('/published', [\App\Http\Controllers\Dashboard\Api\CourseController::class, 'published'])->name('published');
        Route::get('/draft', [\App\Http\Controllers\Dashboard\Api\CourseController::class, 'draft'])->name('draft');

        // Parameterized routes (placed after specific routes)
        Route::get('/{id}', [\App\Http\Controllers\Dashboard\Api\CourseController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Dashboard\Api\CourseController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Dashboard\Api\CourseController::class, 'destroy'])->name('destroy');
    });

    // Packages CRUD (Admin only)
    Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\PackageController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Dashboard\Api\PackageController::class, 'store'])->name('store');

        // Additional endpoints (must come before {id} routes)
        Route::get('/published', [\App\Http\Controllers\Dashboard\Api\PackageController::class, 'published'])->name('published');
        Route::get('/draft', [\App\Http\Controllers\Dashboard\Api\PackageController::class, 'draft'])->name('draft');

        // Parameterized routes (placed after specific routes)
        Route::get('/{id}', [\App\Http\Controllers\Dashboard\Api\PackageController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Dashboard\Api\PackageController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Dashboard\Api\PackageController::class, 'destroy'])->name('destroy');
    });

    // Lessons CRUD (Admin only)
    Route::prefix('lessons')->name('lessons.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\LessonController::class, 'index'])->name('index');
        Route::get('/course/{courseId}', [\App\Http\Controllers\Dashboard\Api\LessonController::class, 'byCourse'])->name('by_course');
        Route::post('/', [\App\Http\Controllers\Dashboard\Api\LessonController::class, 'store'])->name('store');
        Route::get('/free', [\App\Http\Controllers\Dashboard\Api\LessonController::class, 'free'])->name('free');
        Route::post('/reorder', [\App\Http\Controllers\Dashboard\Api\LessonController::class, 'reorder'])->name('reorder');
        Route::get('/search', [\App\Http\Controllers\Dashboard\Api\LessonController::class, 'search'])->name('search');

        // Parameterized routes (placed after specific routes)
        Route::get('/{id}', [\App\Http\Controllers\Dashboard\Api\LessonController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Dashboard\Api\LessonController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Dashboard\Api\LessonController::class, 'destroy'])->name('destroy');
    });

    // Site Settings (Admin only)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\SiteSettingController::class, 'show'])->name('show');
        Route::put('/', [\App\Http\Controllers\Dashboard\Api\SiteSettingController::class, 'update'])->name('update');
    });

    // Notifications (Admin only)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\NotificationController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Dashboard\Api\NotificationController::class, 'store'])->name('store');
        Route::get('/students', [\App\Http\Controllers\Dashboard\Api\NotificationController::class, 'students'])->name('students');
        Route::get('/instructors', [\App\Http\Controllers\Dashboard\Api\NotificationController::class, 'instructors'])->name('instructors');
        Route::get('/{id}', [\App\Http\Controllers\Dashboard\Api\NotificationController::class, 'show'])->name('show');
        Route::delete('/{id}', [\App\Http\Controllers\Dashboard\Api\NotificationController::class, 'destroy'])->name('destroy');
    });

    // Course Summaries (Admin only)
    Route::prefix('course-summaries')->name('course-summaries.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\CourseSummaryController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Dashboard\Api\CourseSummaryController::class, 'store'])->name('store');
        Route::get('/courses', [\App\Http\Controllers\Dashboard\Api\CourseSummaryController::class, 'courses'])->name('courses');
        Route::get('/{id}', [\App\Http\Controllers\Dashboard\Api\CourseSummaryController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Dashboard\Api\CourseSummaryController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Dashboard\Api\CourseSummaryController::class, 'destroy'])->name('destroy');
    });

    // Exams (Admin only)
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\ExamController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Dashboard\Api\ExamController::class, 'store'])->name('store');
        Route::get('/courses', [\App\Http\Controllers\Dashboard\Api\ExamController::class, 'courses'])->name('courses');
        Route::get('/{id}', [\App\Http\Controllers\Dashboard\Api\ExamController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Dashboard\Api\ExamController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Dashboard\Api\ExamController::class, 'destroy'])->name('destroy');
    });

    // Live Streams (Admin only)
    Route::prefix('live-streams')->name('live-streams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\LiveStreamController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Dashboard\Api\LiveStreamController::class, 'store'])->name('store');
        Route::get('/courses', [\App\Http\Controllers\Dashboard\Api\LiveStreamController::class, 'courses'])->name('courses');
        Route::get('/{id}', [\App\Http\Controllers\Dashboard\Api\LiveStreamController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Dashboard\Api\LiveStreamController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Dashboard\Api\LiveStreamController::class, 'destroy'])->name('destroy');
    });

    // Instructors CRUD (Admin only)
    Route::prefix('instructors')->name('instructors.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\Api\InstructorController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Dashboard\Api\InstructorController::class, 'store'])->name('store');

        // Additional endpoints (must come before {id} routes)
        Route::get('/active', [\App\Http\Controllers\Dashboard\Api\InstructorController::class, 'active'])->name('active');
        Route::get('/inactive', [\App\Http\Controllers\Dashboard\Api\InstructorController::class, 'inactive'])->name('inactive');

        // Parameterized routes (placed after specific routes)
        Route::get('/{id}', [\App\Http\Controllers\Dashboard\Api\InstructorController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Dashboard\Api\InstructorController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Dashboard\Api\InstructorController::class, 'destroy'])->name('destroy');
    });

    // Admin Wallet Routes (require admin role)
    Route::prefix('wallet')->name('api.wallet.admin.')->group(function () {
        // Get any user's transactions
        Route::get('/user/{user}/transactions', [\App\Http\Controllers\Api\WalletController::class, 'userTransactions'])->name('user.transactions');
    });
});

