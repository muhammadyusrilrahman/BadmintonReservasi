<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    protected $code = 403;

    public function __construct(string $message = 'Anda tidak memiliki akses untuk melakukan tindakan ini', int $code = 403, ?\Throwable $previous = null)
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

        abort(403, $this->getMessage());
    }
}
