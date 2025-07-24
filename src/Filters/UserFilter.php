<?php

namespace SrvKit\Auth\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services as ConfigServices;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Models\UserModel;

/**
 * Chain Authentication Filter.
 *
 * Checks all authentication systems specified within
 * `Config\Auth->authenticationChain`
 */
class UserFilter implements FilterInterface
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
        $response = Services::response();
        $uri = $request->getUri();
        $segment = $uri->getSegment(1, '+1');
        $iusername = $segment;

        $userModel = new UserModel();
        /** @var User $user [description] */
        $user = $userModel->where('username', $iusername)->first();
        if(!$user){
            return $response->setStatusCode(403)->setBody('Invalid Request');
        }

        $session = Services::srvkitsession();
        $session->set('user', $user);

        // return;
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