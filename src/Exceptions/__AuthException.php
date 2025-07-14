<?php

namespace SrvKit\Auth\Exceptions;

use Throwable;
use Exception;

class AuthException extends Exception
{
    public function __construct(string $message, int|string $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
