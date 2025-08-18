<?php

namespace SrvKit\Auth\Utils\Handlers\Exception;

use Srvkit\Auth\Exceptions\AuthException;
use CodeIgniter\Debug\BaseExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\IncomingRequest;
use SrvKit\Auth\Exceptions\APIResponseException;
use SrvKit\Auth\Utils\Handlers\Response\APIResponse;
use Throwable;

class AuthExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerInterface
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
            $errors = $this->collectErrorDetails($exception);
            $response = APIResponse::error($message, $statusCode, $errors);
            $response->send();
        } else {
            $viewPath = VENDORPATH.'srvkit\auth\src\Views\errors\html.php';
            $this->render($exception, $statusCode, $viewPath);
        }

        exit($exitCode);
    }

    protected function determineMessage(Throwable $exception, int $statusCode): string
    {
        if ($exception instanceof AuthException || $exception instanceof APIResponseException) {
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
        $errors = [];
        $code = method_exists($exception, 'getErrorCode') ? $exception->getErrorCode() : $exception->getCode();

        $error = ['message' =>$exception->getMessage(), 'code' => $code];
        if($exception instanceof APIResponseException){
            $error['details'] = $exception->getDetails();
        }


        if (ENVIRONMENT !== 'production') {
            $error['trace'] = $exception->getTrace();
        }

        $errors[] = $error;

        return $errors;
    }

    /**
     * Checks if incoming request is from api or ajax
     * @return boolean [description]
     */
    protected function isApiRequest()
    {
        /** @var IncomingRequest */
        $request = service('request');
        return strpos($request->getPath(), 'api/') !== false || $request->isAJAX();
    }
}