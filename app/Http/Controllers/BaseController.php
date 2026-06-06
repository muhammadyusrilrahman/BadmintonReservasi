<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

abstract class BaseController extends Controller
{
    /**
     * Return a success JSON response.
     */
    protected function successResponse(mixed $data = null, string $message = 'Berhasil', int $code = 200): JsonResponse
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
    protected function errorResponse(string $message = 'Terjadi kesalahan', int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Redirect with success flash message.
     */
    protected function redirectWithSuccess(string $route, string $message = 'Berhasil'): RedirectResponse
    {
        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Redirect back with success flash message.
     */
    protected function backWithSuccess(string $message = 'Berhasil'): RedirectResponse
    {
        return back()->with('success', $message);
    }

    /**
     * Redirect with error flash message.
     */
    protected function redirectWithError(string $route, string $message = 'Terjadi kesalahan'): RedirectResponse
    {
        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Redirect back with error flash message.
     */
    protected function backWithError(string $message = 'Terjadi kesalahan'): RedirectResponse
    {
        return back()->with('error', $message);
    }
}
