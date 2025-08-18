<?php

namespace SrvKit\Auth\Exceptions;

use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\Exceptions\HTTPExceptionInterface;

/**
 * Custom exception for API-related errors with HTTP status codes.
 */
class APIResponseException extends FrameworkException implements HTTPExceptionInterface
{
    /** @var string errorCode custom error codes */
    protected $errorCode = '';

    /**
     * Get custom error code
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Set custom error codes
     * @param int|string $code
     * @return self
     */
    protected function setErrorCode(int|string $code): self
    {
        $this->errorCode = $code;
        return $this;
    }

    /**
     * @var array Additional error details
     */
    protected $details = [];

    /**
     * Get additional error details.
     *
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * Set additional error details.
     *
     * @param array $details Error details
     * @return $this
     */
    protected function setDetails(array $details): self
    {
        $this->details = $details;
        return $this;
    }

    /**
     * Throws an exception when the user is not allowed to access a resource.
     *
     * @param string $message Custom error message
     * @return static
     */
    public static function forActionNotAllowed(string $message = 'You are not allowed to access this resource.')
    {
        return new static($message, 403);
    }

    /**
     * Throws an exception for invalid or malformed requests.
     *
     * @param string $message Custom error message
     * @return static
     */
    public static function forInvalidRequest(string $message = 'Invalid request.')
    {
        return new static($message, 400);
    }

    /**
     * Throws an exception for validation failures.
     *
     * @param string $message Custom error message
     * @param array $errors Validation error details
     * @return static
     */
    public static function forValidationFailed(string $message = 'Validation failed.', array $errors = [])
    {
        $exception = new static($message, 400);
        return $exception->setDetails($errors);
    }

    /**
     * Throws an exception for forbidden requests.
     *
     * @param string $message Custom error message
     * @return static
     */
    public static function forForbidden(string $message = 'Forbidden request.')
    {
        $exception = new static($message, 403);
        return $exception->setErrorCode(ErrorCodes::E4003_RESOURCE_GONE);
    }

    /**
     * Throws an exception for unauthenticated requests.
     *
     * @param string $message Custom error message
     * @return static
     */
    public static function forUnauthorized(string $message = 'Authentication required.')
    {
        return new static($message, 401);
    }

    /**
     * Throws an exception for resources that cannot be found.
     *
     * @param string $message Custom error message
     * @return static
     */
    public static function forNotFound(string $message = 'Resource not found.', string $path = '')
    {
        $exception = new static($message, 404);
        return $exception->setDetails(['path' => $path]);
    }

    public static function forInvalidJSON(string $message = 'Invalid JSON')
    {
        return new static($message, 400);
    }

    public static function forUnexpected(string $message)
    {
        $exception = new static($message, 400);
        return $exception->setErrorCode(ErrorCodes::E9999_UNKNOWN_ERROR);
    }
}