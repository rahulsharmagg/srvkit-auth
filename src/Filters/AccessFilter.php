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
class AccessFilter implements FilterInterface
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
        $response = Services::response();
        $accessToken = "";

        if($request->isAJAX()){
            $authorization = $request->header('Authorization');
            if (preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
                $accessToken = $matches[1];
            }
        } else {
            $session = Services::srvkitsession();

            ['access' => $access] = $session->get('__srvkit_accesstoken__');
            if($access){
                $accessToken = $access["token"];
            }
        }
        
        // dd($accessToken);

        if(!empty($accessToken)){
            $auth = Services::auth();
            try {
                $payload = $auth->verifyAccessToken($accessToken);
                $data = $payload->data;

                return null;
            } catch (AuthException $e) {
                return $this->autoRespond([
                    'error' => [
                        "message" => $e->getMessage(),
                        "code" => $e->getErrorCode()
                    ]
                ], 401, 'error');
            }  
        }
        
        return $this->autoRespond([
            'error' => [
                "message" => 'Access token is required to access this page.',
                "code" => 403
            ]
        ], 403, 'error');
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