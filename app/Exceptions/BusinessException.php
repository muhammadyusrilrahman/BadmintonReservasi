<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    /**
     * The HTTP status code for business logic errors.
     */
    protected $code = 422;

    /**
     * Create a new business exception instance.
     */
    public function __construct(string $message = 'Terjadi kesalahan pada proses bisnis', int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception.
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
            ], $this->getCode());
        }

        return back()->with('error', $this->getMessage());
    }
}
