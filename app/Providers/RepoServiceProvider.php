<?php

namespace App\Providers;

use App\Repository\AttendanceRepository;
use App\Interface\AttendanceRepositoryInterface;
use App\Repository\ExamRepository;
use App\Interface\ExamRepositoryInterface;
use App\Repository\LibraryRepository;
use App\Interface\LibraryRepositoryInterface;
use App\Repository\QuizRepository;
use App\Interface\QuizRepositoryInterface;
use App\Repository\SubjectsRepository;
use App\Interface\SubjectsRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            'App\Interface\TeacherRepositoryInterface',
            'App\Repository\TeacherRepository'
        );
        $this->app->bind(
            'App\Interface\StudentRepositoryInterface',
            'App\Repository\StudentRepository'
        );
        $this->app->bind(
            'App\Interface\PromotionRepositoryInterface',
            'App\Repository\PromotionRepository'
        );
        $this->app->bind(
            'App\Interface\FeeRepositoryInterface',
            'App\Repository\FeeRepository'
        );
        $this->app->bind(
            'App\Interface\FeeInvoicesRepositoryInterface',
            'App\Repository\FeeInvoicesRepository'
        );
        $this->app->bind(
            'App\Interface\ReceiptStudentRepositoryInterface',
            'App\Repository\ReceiptStudentRepository'
        );
        $this->app->bind(
            'App\Interface\ProcessingFeesRepositoryInterface',
            'App\Repository\ProcessingFeesRepository'
        );
        $this->app->bind(
            'App\Interface\PaymentStudentRepositoryInterface',
            'App\Repository\PaymentStudentRepository'
        );
        $this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
        $this->app->bind(SubjectsRepositoryInterface::class, SubjectsRepository::class);
        $this->app->bind(QuizRepositoryInterface::class, QuizRepository::class);
        $this->app->bind(LibraryRepositoryInterface::class, LibraryRepository::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
