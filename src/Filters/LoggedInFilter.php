<?php

namespace SrvKit\Auth\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services as ConfigServices;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Exceptions\AuthException;

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
            $auth = service('auth');
            $loggedIn = $auth->loggedIn();
            $view = \Config\Services::renderer();
            if(!$loggedIn) throw new AuthException('Login session has been expired', 'E20401');

            $view->setVar('isLoggedIn', $loggedIn);
            $view->setVar('user', $auth->user());
            return;
        } catch (AuthException $e) {
            if($request->isAJAX()){
                return $this->autoRespond(["error" => ["message" => $e->getMessage(), "code" => $e->getCode()], "type" => "error"], 401);
            }
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