<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse($data = null, string $message = 'Operation successful.', int $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse(string $message = 'An error occurred.', int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
        ], $code);
    }
}
