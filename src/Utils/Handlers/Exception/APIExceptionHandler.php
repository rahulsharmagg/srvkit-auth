<?php

namespace SrvKit\Auth\Utils\Handlers\Exception;

use Srvkit\Auth\Exceptions\AuthException;
use CodeIgniter\Debug\BaseExceptionHandler;
use CodeIgniter\Debug\ExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\IncomingRequest;
use SrvKit\Auth\Exceptions\APIResponseException;
use SrvKit\Auth\Utils\Handlers\Response\APIResponse;
use Throwable;

class APIExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerInterface
{
    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode
    ): void {
        if ($this->isApiRequest()) {
            $message = $this->determineMessage($exception, $statusCode);
            $details = $this->collectErrorDetails($exception);
            $errorCode = method_exists($exception, 'getErrorCode') ? $exception->getErrorCode() : $exception->getCode();
            $response = APIResponse::error($message, $statusCode, $errorCode, $details);
            $response->send();
        } else {
            (new ExceptionHandler(config('Exceptions')))->handle($exception, $request, $response, $statusCode, $exitCode);
        }

        exit($exitCode);
    }

    protected function determineMessage(Throwable $exception, int $statusCode): string
    {
        if ($exception instanceof APIResponseException) {
            return $exception->getMessage();
        }

        switch ($statusCode) {
            case 404:
                return 'Resource not found';
            case 400:
                return 'Bad request';
            case 500:
                return 'Internal server error';
            default:
                return $exception->getMessage() ?: 'An unexpected error occurred';
        }
    }

    protected function collectErrorDetails(Throwable $exception): array
    {
        $error = [];
        if($exception instanceof APIResponseException && !empty($exception->getDetails())){
            $error['details'] = $exception->getDetails();
        }

        return $error;
    }

    /**
     * Checks if incoming request is from api or ajax
     * @return boolean [description]
     */
    protected function isApiRequest()
    {
        /** @var IncomingRequest */
        $request = service('request');

        $apiPath = config('Auth')->apiPath;

        return strpos($request->getPath(), $apiPath) !== false || $request->isAJAX();
    }
}