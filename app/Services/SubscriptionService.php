<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Package;
use App\Models\CourseStudent;
use App\Models\PackageStudent;
use App\Interfaces\WalletTransactionRepositoryInterface;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use stdClass;

class SubscriptionService
{
    protected $transactionRepository;
    protected $walletService;

    public function __construct(WalletTransactionRepositoryInterface $transactionRepository, WalletService $walletService)
    {
        $this->transactionRepository = $transactionRepository;
        $this->walletService = $walletService;
    }

    /**
     * Subscribe a user to a course using wallet balance
     *
     * @param User $user
     * @param Course $course
     * @param array $options ['expires_at' => datetime|null, 'description' => string|null]
     * @return array ['subscription' => CourseStudent, 'transaction' => WalletTransaction, 'remaining_balance' => float]
     * @throws \Exception
     */
    public function subscribeToCourse(User $user, Course $course, array $options = [])
    {
        // Check if already enrolled (relationship automatically excludes soft-deleted)
        $existing = $user->enrolledCourses()
            ->where('course_id', $course->id)
            ->exists();

        if ($existing) {
            throw new \Exception(__('auth.already_enrolled'));
        }

        // Check if course is free
        if ($course->is_free || $course->price == 0) {
            // Free course, just enroll
            $subscription = CourseStudent::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'expires_at' => $options['expires_at'] ?? null,
            ]);

            return [
                'subscription' => $subscription,
                'transaction' => null,
                'remaining_balance' => $user->balance,
            ];
        }

        // Check if user has sufficient balance
        if ($user->balance < $course->price) {
            throw new \Exception(__('auth.insufficient_balance_subscription', ['amount' => $course->price]));
        }

        return DB::transaction(function () use ($user, $course, $options) {
            // Deduct from wallet using WalletService (creates transaction with positive amount, type 'charge')
            $transaction = $this->walletService->withdraw(
                $user,
                $course->price,
                'charge',
                $options['description'] ?? __('auth.course_subscription', ['title' => $course->title]),
                $course->id,
                Course::class
            );

            // Create enrollment record
            $subscription = CourseStudent::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'wallet_transaction_id' => $transaction->id,
                'enrolled_at' => now(),
                'expires_at' => $options['expires_at'] ?? null,
            ]);

            return [
                'subscription' => $subscription,
                'transaction' => $transaction,
                'remaining_balance' => $user->fresh()->balance,
            ];
        });
    }

    /**
     * Subscribe a user to a package using wallet balance
     *
     * @param User $user
     * @param Package $package
     * @param array $options ['expires_at' => datetime|null, 'description' => string|null]
     * @return array ['subscription' => PackageStudent, 'transaction' => WalletTransaction, 'remaining_balance' => float]
     * @throws \Exception
     */
    public function subscribeToPackage(User $user, Package $package, array $options = [])
    {
        // Check if already subscribed (relationship automatically excludes soft-deleted)
        $existing = $user->subscribedPackages()
            ->where('package_id', $package->id)
            ->exists();

        if ($existing) {
            throw new \Exception(__('auth.already_subscribed'));
        }

        // Check if package is free
        if ($package->price == 0) {
            // Free package, just subscribe
            $subscription = PackageStudent::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'subscribed_at' => now(),
                'expires_at' => $options['expires_at'] ?? null,
            ]);

            return [
                'subscription' => $subscription,
                'transaction' => null,
                'remaining_balance' => $user->balance,
            ];
        }

        // Check if user has sufficient balance
        if ($user->balance < $package->price) {
            throw new \Exception(__('auth.insufficient_balance_subscription', ['amount' => $package->price]));
        }

        return DB::transaction(function () use ($user, $package, $options) {
            // Deduct from wallet using WalletService (creates transaction with positive amount, type 'charge')
            $transaction = $this->walletService->withdraw(
                $user,
                $package->price,
                'charge',
                $options['description'] ?? __('auth.package_subscription', ['title' => $package->title]),
                $package->id,
                Package::class
            );

            // Create subscription record
            $subscription = PackageStudent::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'wallet_transaction_id' => $transaction->id,
                'subscribed_at' => now(),
                'expires_at' => $options['expires_at'] ?? null,
            ]);

            // Auto-enroll user in all courses included in the package
            $courses = $package->courses;
            foreach ($courses as $course) {
                // Check if not already enrolled (use relationship)
                $alreadyEnrolled = $user->enrolledCourses()
                    ->where('course_id', $course->id)
                    ->exists();

                if (!$alreadyEnrolled) {
                    CourseStudent::create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'wallet_transaction_id' => $transaction->id, // same transaction
                        'enrolled_at' => now(),
                        'expires_at' => $options['expires_at'] ?? null,
                    ]);
                }
            }

            return [
                'subscription' => $subscription,
                'transaction' => $transaction,
                'remaining_balance' => $user->fresh()->balance,
                'courses_enrolled' => $courses->count(),
            ];
        });
    }

    /**
     * Get user's course enrollments
     */
    public function getUserEnrollments(User $user, array $filters = [])
    {
        $query = $user->enrolledCourses();

        if (isset($filters['active_only'])) {
            $query->where(function($q) {
                $q->whereNull('course_student.expires_at')
                  ->orWhere('course_student.expires_at', '>', now());
            });
        }

        return $query->get();
    }

    /**
     * Get user's package subscriptions
     */
    public function getUserSubscriptions(User $user, array $filters = [])
    {
        $query = $user->subscribedPackages();

        if (isset($filters['active_only'])) {
            $query->where(function($q) {
                $q->whereNull('package_student.expires_at')
                  ->orWhere('package_student.expires_at', '>', now());
            });
        }

        return $query->get();
    }

    /**
     * Get all courses the user has access to (direct enrollments + package courses)
     * Returns a collection of objects with:
     * - course (Course model)
     * - enrolled_at (datetime)
     * - expires_at (datetime|null)
     * - source ('direct'|'package')
     * - package (object|null) - package info if source is 'package'
     *
     * @param User $user
     * @param array $filters ['active_only' => bool]
     * @return \Illuminate\Support\Collection
     */
    public function getAccessibleCourses(User $user, array $filters = []): \Illuminate\Support\Collection
    {
        // Direct course enrollments
        $direct = $user->enrolledCourses()
            ->wherePivot('deleted_at', null)
            ->with(['category', 'instructor'])
            ->get()
            ->map(function($course) {
                $data = new stdClass();
                $data->id = $course->id;
                $data->course = $course;
                $data->enrolled_at = $course->pivot->enrolled_at ? \Carbon\Carbon::parse($course->pivot->enrolled_at) : null;
                $data->expires_at = $course->pivot->expires_at ? \Carbon\Carbon::parse($course->pivot->expires_at) : null;
                $data->source = 'direct';
                $data->package = null;
                return $data;
            });

        // Courses from packages the user is subscribed to
        $packageCourses = collect();
        $packages = $user->subscribedPackages()
            ->wherePivot('deleted_at', null)
            ->with(['courses.category', 'courses.instructor'])
            ->get();

        foreach ($packages as $package) {
            foreach ($package->courses as $course) {
                $data = new stdClass();
                $data->id = $course->id;
                $data->course = $course;
                $data->enrolled_at = $package->pivot->subscribed_at ? \Carbon\Carbon::parse($package->pivot->subscribed_at) : null;
                $data->expires_at = $package->pivot->expires_at ? \Carbon\Carbon::parse($package->pivot->expires_at) : null;
                $data->source = 'package';
                $data->package = (object)[
                    'id' => $package->id,
                    'title' => $package->title,
                    'slug' => $package->slug,
                ];
                $packageCourses->push($data);
            }
        }

        // Merge: give priority to direct enrollments (direct overrides package if duplicate)
        $merged = $packageCourses->keyBy('id')->merge($direct->keyBy('id'))->values();

        // Apply active_only filter if requested
        if (isset($filters['active_only'])) {
            $merged = $merged->filter(function($item) {
                $expires = $item->expires_at;
                return is_null($expires) || $expires->isFuture();
            })->values();
        }

        return $merged;
    }
}
