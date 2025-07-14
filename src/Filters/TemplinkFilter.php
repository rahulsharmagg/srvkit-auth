<?php

namespace SrvKit\Auth\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services as ConfigServices;
use SrvKit\Auth\Config\Services;

/**
 * Chain Authentication Filter.
 *
 * Checks all authentication systems specified within
 * `Config\Auth->authenticationChain`
 */
class TemplinkFilter implements FilterInterface
{
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

        try {
            $token = $request->getGetPost('token');
            $tempLink = Services::tempLink();
            $action = $tempLink->verify($token)->getAction();
            if($request->is('get')){
                return ConfigServices::redirectresponse()->to('auth/action/'.$action.'?token='.$token);
            }
        } catch (\Exception $e) {
            return ConfigServices::response()->setStatusCode(403)->appendBody($e->getMessage());
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