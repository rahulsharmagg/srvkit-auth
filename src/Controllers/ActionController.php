<?php

namespace SrvKit\Auth\Controllers;

use Exception;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Entities\User; // Assuming these are needed for some actions
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Models\UserModel;
use SrvKit\Auth\Traits\ActionTrait;

class ActionController extends BaseController
{
    use ActionTrait;
    use ResponseTrait;

    // The _remap method will intercept all calls to this controller
    public function _remap(string $method, ...$params)
    {
        // Treat the first segment after the controller as the action name
        $actionName = $params[0];

        // Handle the 'for' GET parameter for special view rendering
        $for = $this->request->getGet('for');
        if ($for) {
            return $this->action($for)->view(data: ['action' => 'message-block', 'name' => $for]);
        }

        try {
            
            $allowedActions = $this->config->actions ?? [];
            
            if (!in_array($actionName, $allowedActions, true)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)->setBody('Action not allowed');
            }

            // Convert kebab-case to camelCase for method name
            $actionMethod = str_replace('-', '', lcfirst(ucwords($actionName, '-')));

            // Check if the method exists and is callable (public)
            if (!method_exists($this, $actionMethod) || !is_callable([$this, $actionMethod])) {
                // If the action method doesn't exist or isn't callable, return 404
                return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setBody('Action not found');
            }

            // Call the dynamic action method with any remaining parameters
            return $this->$actionMethod(...$params);

        } catch (\Throwable $e) {
            // Centralized error handling for all actions dispatched via _remap
            // Use CI's logger for better error tracking
            log_message('error', 'Error in ActionController::_remap calling {actionName}: {exception}', [
                'actionName' => $actionName,
                'exception' => $e
            ]);

            // Differentiate between AuthException and general Exception for API responses
            if ($e instanceof AuthException) {
                return $this->respond([
                    'error' => ['message' => $e->getMessage(), 'code' => $e->getErrorCode()]
                ], ResponseInterface::HTTP_UNAUTHORIZED, 'Authentication Error'); // 401 for auth errors
            } else {
                return $this->respond([
                    'error' => ['message' => 'An unexpected error occurred.', 'code' => $e->getCode()]
                ], ResponseInterface::HTTP_BAD_REQUEST, 'Unexpected Error'); // 400 for general issues
            }
        }
    }

    // Existing action methods remain the same, just removed the action() dispatcher method
    // --------------- Add action controller from here ------------------
    //
    //
    public function logout(): ResponseInterface
    {
        $auth = service('auth');
        $auth->logout();
        return redirect()->to('auth/login')->with('message', 'success:Logout Successfully');
    }

    public function refreshToken(): ResponseInterface
    {
        try {
            $auth = Services::auth();
            $token = $auth->verifyRefreshToken();
            $user = $token->getUser();
            $accessToken = $auth->generateAccessToken(['username' => $user->username, 'role' => $user->role]);

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

    /**
     * GET: Render the forgotpassword page
     * @return [type] [description]
     */
    public function forgotPassword($action)
    {
        return $this->action($action)->view();
    }

    public function resetPassword($action)
    {
        if($this->request->is('get')){
           return $this->action($action)->view();
        }

        try {
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');

            /**
             * Check if request is xmlhttprequest
             */
            if ($this->request->isAJAX()) {
                $payload = $this->request->getJSON();
                $username = $payload->username;
                $email = $payload->email;
            }

            $userModel = new UserModel();

            /** @var ?User [description] */
            $user = $userModel->where('username', $username)->where('email', $email)->first();
            if ($user) {
                $tempLink = Services::tempLink();
                $tempLink->for($user)->action('reset-password')->create();
                $tempLink->save();
                $link = $tempLink->link();
                // dd($link); // send email from here
                $message = 'An email has been successfully sent to <b>"' . $user->email . '"</b>, check and follow the password recovery link';

                if ($this->request->isAJAX()) {
                    return $this->respond([
                        'message' => $message,
                        'status' => 'success',
                        'dump' => $link
                    ]);
                }

                return redirect()->to('auth/action?for=forgot-password')->with('message', 'success:' . $message)->with('icon', 'success');
            } else {
                throw new AuthException('Incorrect username or email', 'E20245');
            }

        } catch (AuthException $e) {
            if ($this->request->isAJAX()) {
                return $this->respond([
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getErrorCode()
                    ]
                ]);
            }
            return redirect()->back()->with('message', 'error:' . $e->getMessage())->withInput();
        }
    }

    public function updatePassword()
    {
        // ... (your existing updatePassword logic) ...
        try {
            $tempLink = Services::tempLink();
            $tempLink->init($this->request->getPost('token'));

            if (!$this->validate('new-password')) {
                $errors = array_values($this->validator->getErrors());
                return redirect()->back()->withInput()->with('message', 'error:' . $errors[0]);
            }

            $newPassword = $this->request->getPost('password');
            $userModel = new UserModel();
            $userModel->where('id', $tempLink->user->id)->set('password', $newPassword)->update();
            $tempLink->use();
            $message = 'Password Update Successfully.';
            return redirect()->to('auth/action?for=reset-password')->with('message', 'success:' . $message)->with('icon', 'success');
        } catch (AuthException $e) {
            return redirect()->back()->withInput()->with('message', 'error:' . $e->getMessage());
        }
    }
}