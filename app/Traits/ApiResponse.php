<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     */
    protected function apiSuccess(mixed $data = null, string $message = 'Berhasil', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    protected function apiError(string $message = 'Terjadi kesalahan', int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Return a created JSON response (201).
     */
    protected function apiCreated(mixed $data = null, string $message = 'Data berhasil dibuat'): JsonResponse
    {
        return $this->apiSuccess($data, $message, 201);
    }

    /**
     * Return a no content response (204).
     */
    protected function apiNoContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Return a not found response (404).
     */
    protected function apiNotFound(string $message = 'Data tidak ditemukan'): JsonResponse
    {
        return $this->apiError($message, 404);
    }

    /**
     * Return an unauthorized response (403).
     */
    protected function apiForbidden(string $message = 'Anda tidak memiliki akses'): JsonResponse
    {
        return $this->apiError($message, 403);
    }
}
