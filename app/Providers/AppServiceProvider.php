<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind Repositories to Interfaces
        $this->app->bind(
            \App\Interfaces\CategoryRepositoryInterface::class,
            \App\Repositories\CategoryRepository::class
        );

        $this->app->bind(
            \App\Interfaces\StudentRepositoryInterface::class,
            \App\Repositories\StudentRepository::class
        );

        $this->app->bind(
            \App\Interfaces\CourseRepositoryInterface::class,
            \App\Repositories\CourseRepository::class
        );

        $this->app->bind(
            \App\Interfaces\InstructorRepositoryInterface::class,
            \App\Repositories\InstructorRepository::class
        );

        $this->app->bind(
            \App\Interfaces\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );

        $this->app->bind(
            \App\Interfaces\PackageRepositoryInterface::class,
            \App\Repositories\PackageRepository::class
        );

        $this->app->bind(
            \App\Interfaces\LessonRepositoryInterface::class,
            \App\Repositories\LessonRepository::class
        );

        $this->app->bind(
            \App\Interfaces\SiteSettingRepositoryInterface::class,
            \App\Repositories\SiteSettingRepository::class
        );

        $this->app->bind(
            \App\Interfaces\WalletTransactionRepositoryInterface::class,
            \App\Repositories\WalletTransactionRepository::class
        );

        $this->app->bind(
            \App\Interfaces\NotificationRepositoryInterface::class,
            \App\Repositories\NotificationRepository::class
        );

        $this->app->bind(
            \App\Interfaces\CourseSummaryRepositoryInterface::class,
            \App\Repositories\CourseSummaryRepository::class
        );

        $this->app->bind(
            \App\Interfaces\ExamRepositoryInterface::class,
            \App\Repositories\ExamRepository::class
        );

        $this->app->bind(
            \App\Interfaces\LiveStreamRepositoryInterface::class,
            \App\Repositories\LiveStreamRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
    }
}
