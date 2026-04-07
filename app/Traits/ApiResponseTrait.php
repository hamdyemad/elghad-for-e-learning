<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

trait ApiResponseTrait
{
    protected function successResponse($data = [], string $message = 'Operation successful', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => []
        ], $statusCode);
    }

    protected function errorResponse(string $message = 'Operation failed', $errors = [], int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => [],
            'errors' => is_array($errors) ? $errors : [$errors]
        ], $statusCode);
    }

    protected function createdResponse($data, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    protected function noContentResponse(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return $this->successResponse(null, $message, 200);
    }

    protected function formatPagination($paginator): array
    {
        return [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more' => $paginator->hasMorePages(),
        ];
    }

    /**
     * Redirect back with success message (for web controllers)
     */
    protected function successRedirect(string $route = null, string $message = 'Operation successful'): RedirectResponse
    {
        if ($route) {
            return redirect($route)->with('success', $message);
        }
        return back()->with('success', $message);
    }

    /**
     * Redirect back with error message (for web controllers)
     */
    protected function errorRedirect(string $route = null, string $message = 'Operation failed'): RedirectResponse
    {
        if ($route) {
            return redirect($route)->with('error', $message);
        }
        return back()->with('error', $message);
    }
}
