<?php

namespace SrvKit\Auth\Exceptions;

use Throwable;
use Exception;

class AuthException extends Exception
{
    protected $errorCode;

    public function __construct(string $message, string $code = '', ?Throwable $previous = null)
    {
        $intCode = is_numeric($code) ? (int) $code : 0;
        parent::__construct($message, $intCode, $previous);
        $this->errorCode = $code;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
