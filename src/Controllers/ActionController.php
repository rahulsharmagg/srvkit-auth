<?php

namespace SrvKit\Auth\Controllers;

use Exception;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Models\UserModel;

class ActionController extends BaseController
{   
    use ResponseTrait;

    public function index(string $action)
    {
        $auth = Services::auth();
        $for = $this->request->getGet('for');
        if($for){
            return $auth->action($for)->view(data: ['action' => 'message-block', 'name' => $for]);
        }

        if ($action) {
            return $auth->action($action)->view();
        }

        throw new PageNotFoundException('Page not found.');
    }

    public function action(string $actionName)
    {
        try {
            $allowed = $this->config->actions;
            if (!in_array($actionName, $allowed, true)) {
                return $this->response->setStatusCode(403)->setBody('Action not allowed');
            }
            $actionMethod = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
            if(!method_exists($this, $actionMethod)){
                return $this->response->setStatusCode(404)->setBody('Action not found');
            }

            return $this->$actionMethod();
        } catch (\Throwable $e) {
            return $this->response
                            ->setStatusCode(500)
                            ->setBody('Error calling action: ' . $e->getMessage());
        }
    }


    // --------------- Add action controller from here ------------------
    // 
    public function refreshToken(): ResponseInterface
    {
        try {
            $auth = Services::auth();
            $token = $auth->verifyRefreshToken();
            $user = $token->getUser();
            $accessToken = $auth->generateAccessToken(['username' => $user->username]);

            $access = [
                'token' => $accessToken,
                'type' => $user->role 
            ];

            return $this->respond([
                'message' => 'Access token granted.',
                'status' => 'success',
                'access' => $access
            ], 200);
        } catch (AuthException $e) {
            return $this->respond([
                'error' => ['message' => $e->getMessage(), 'code' => $e->getCode()]
            ], 401, 'Authentication Error');
        } catch (Exception $e) {
            return $this->respond([
                'error' => ['message' => $e->getMessage(), 'code' => $e->getCode()]
            ], 400, 'Unexpected Error');
        }
    }


    public function resetPassword(): ResponseInterface
    {
        try {
           
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');

            /**
             * Check if request is xmlhttprequest
             */
            if($this->request->isAJAX()){
                $payload = $this->request->getJSON();
                $username = $payload->username;
                $email = $payload->email;
            }

            $userModel = new UserModel();

            /** @var ?User [description] */
            $user = $userModel->where('username', $username)->where('email', $email)->first();
            if($user) {
                $tempLink = Services::tempLink();
                $tempLink->for($user)->action('reset-password')->create();
                $tempLink->save();
                $link = $tempLink->link();
                // dd($link); // send email from here
                $message = 'An email has been successfully sent to <b>"'.$user->email.'"</b>, check and follow the password recovery link';

                if($this->request->isAJAX()){
                    return $this->respond([
                        'message' => $message,
                        'status' => 'success',
                        'dump' => $link
                    ]);
                }

                return redirect()->to('auth/action?for=forgot-password')->with('message', 'success:'.$message)->with('icon', 'success');
            } else {
                throw new AuthException('Incorrect username or email', 'E20245');
            }

        } catch (AuthException $e) {
            if($this->request->isAJAX()){
                return $this->respond([
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getErrorCode()
                    ]
                ]);
            }
            return redirect()->back()->with('message', 'error:'.$e->getMessage())->withInput();
        }
    }

    public function updatePassword()
    {
        try {
            $tempLink = Services::tempLink();
            $tempLink->init($this->request->getPost('token'));

            if (!$this->validate('new-password')) {
                $errors = array_values($this->validator->getErrors());
                return redirect()->back()->withInput()->with('message', 'error:'.$errors[0]);
            }

            $newPassword = $this->request->getPost('password');
            $userModel = new UserModel();
            $userModel->where('id', $tempLink->user->id)->set('password', $newPassword)->update();
            $tempLink->use();
            $message = 'Password Update Successfully.';
            return redirect()->to('auth/action?for=reset-password')->with('message', 'success:'.$message)->with('icon', 'success');
        } catch (AuthException $e) {
            return redirect()->back()->withInput()->with('message', 'error:'.$e->getMessage());
        }
    }
}
