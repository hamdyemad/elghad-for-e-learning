<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Traits\ApiResponseTrait;
use App\Models\Category;
use App\Models\User;
use App\Models\Course;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $model = $exception->getModel();
                $translationKey = $this->getNotFoundTranslationKey($model);
                $message = __($translationKey);
                return $this->errorResponse(
                    $message,
                    [$message],
                    404
                );
            }

            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return $this->errorResponse(
                    'Validation failed',
                    $exception->errors(),
                    422
                );
            }

            if ($exception instanceof CategoryAlreadyExistsException) {
                return $this->errorResponse(
                    __('categories.already_exists'),
                    [],
                    $exception->getCode()
                );
            }

            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return $this->errorResponse(
                    'Unauthenticated',
                    ['Please login to continue'],
                    401
                );
            }

            if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return $this->errorResponse(
                    'Unauthorized',
                    ['You do not have permission to perform this action'],
                    403
                );
            }

            return $this->errorResponse(
                'An error occurred',
                [config('app.debug') ? $exception->getMessage() : 'Internal server error'],
                500
            );
        }

        return parent::render($request, $exception);
    }

    /**
     * Determine the appropriate translation key for a not-found model
     */
    protected function getNotFoundTranslationKey($model): string
    {
        // Get the fully qualified class name
        $modelClass = is_string($model) ? $model : (is_object($model) ? get_class($model) : null);
        $modelClass = ltrim($modelClass, '\\');

        // Use string check to ensure we catch User model regardless of exact namespace match
        if ($modelClass === 'App\Models\User' || str_contains($modelClass, '\User')) {
            // Check the current route to determine if it's for student or instructor
            $route = request()->route();
            if ($route) {
                $routeName = $route->getName() ?? '';
                // If route name contains 'instructor', it's an instructor not found
                if (str_contains($routeName, 'instructor')) {
                    return 'instructors.not_found';
                }
                // If route name contains 'student' or is students.*, it's a student not found
                if (str_contains($routeName, 'student')) {
                    return 'students.not_found';
                }
            }
            // Default to unknown if route context not available
            return 'Resource not found';
        }

        if ($modelClass === 'App\Models\Category' || str_contains($modelClass, '\Category')) {
            return 'categories.not_found';
        }

        if ($modelClass === 'App\Models\Course' || str_contains($modelClass, '\Course')) {
            return 'courses.not_found';
        }

        return 'Resource not found';
    }
}

