<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Interface\AuthenticationInterface::class,
            \App\Repositories\AuthenticationRepository::class
        );
        $this->app->bind(
            \App\Interface\UserManagementInterface::class,
            \App\Repositories\UserManagementRepository::class
        );
        $this->app->bind(
            \App\Interface\ClassManagementInterface::class,
            \App\Repositories\ClassManagementRepository::class
        );
        $this->app->bind(
            \App\Interface\CourseManagementInterface::class,
            \App\Repositories\CourseManagementRepository::class
        );
        $this->app->bind(
            \App\Interface\ScheduleManagementInterface::class,
            \App\Repositories\ScheduleManagementRepository::class
        );
        $this->app->bind(
            \App\Interface\AssessmentManagementInterface::class,
            \App\Repositories\AssessmentManagementRepository::class
        );
        $this->app->bind(
            \App\Interface\GradeManagementInterface::class,
            \App\Repositories\GradeManagementRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
