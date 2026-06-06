<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    protected $code = 404;

    public function __construct(string $message = 'Data tidak ditemukan', int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
            ], $this->getCode());
        }

        abort(404, $this->getMessage());
    }
}
