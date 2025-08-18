<?php

namespace SrvKit\Auth\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services as ConfigServices;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Config\Session;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Models\UserModel;

/**
 * Chain Authentication Filter.
 *
 * Checks all authentication systems specified within
 * `Config\Auth->authenticationChain`
 */
class LoggedInFilter implements FilterInterface
{
    use \SrvKit\Auth\Traits\ResponseTrait;

    /**
     * Checks authenticators in sequence to see if the user is logged in through
     * either of authenticators.
     *
     * @param array|null $arguments
     *
     * @return RedirectResponse|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!$request instanceof IncomingRequest) {
            return;
        }

        /** @var ResponseInterface $response [description] */
        $response = Services::response();

        try {
            $cookie = $request->getCookie('__srvkit_refreshtoken__');
            if(!$cookie) throw new AuthException('Invalid Request.');
            $auth = Services::auth();
            $token = $auth->verifyRefreshToken($cookie);
            return;
        } catch (AuthException $e) {
            return ConfigServices::redirectresponse()->to('auth/login')->with('message', 'info:Login session expired.');
        }
    }

    /**
     * We don't have anything to do here.
     *
     * @param array|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
        // Nothing required
    }
}