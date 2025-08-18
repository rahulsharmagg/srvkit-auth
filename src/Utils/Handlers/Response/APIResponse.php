<?php

namespace SrvKit\Auth\Utils\Handlers\Response;

use CodeIgniter\HTTP\ResponseInterface;
use SrvKit\Auth\Traits\ResponseTrait;

class APIResponse
{
    public static function success($data = [], string $message = 'Success', int $statusCode = 200): ResponseInterface
    {
        return service('response')->setJSON([
            'status'    => 'success',
            'message' => $message,
            'data'    => $data,
        ])->setStatusCode($statusCode);
    }

    public static function error(string $message = 'An error occurred', int $statusCode = 400, ?string $errorCode = '', $details = []): ResponseInterface
    {
        $response = [
            'status'  => 'error',
            'error' => ['message' => $message, 'code' => $statusCode, ...$details],
            'timestamp' => date('c')
        ];

        if(!empty($errorCode)) {
            $response['error']['code'] = $errorCode;
        }

        return service('response')->setJSON($response)->setStatusCode($statusCode);
    }
}