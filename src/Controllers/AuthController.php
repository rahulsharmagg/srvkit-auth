<?php

namespace SrvKit\Auth\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use SrvKit\Auth\Config\Services;
use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Exceptions\PageNotFoundException;
use DateTime;
use SrvKit\Auth\Models\UserModel;

class AuthController extends Controller
{
    use ResponseTrait;

    public function index($step = false){
        $path = $this->request->getPath();
        if($path == 'auth'){
            return redirect()->to('auth/login');
        }

        if(!$step &&  $path == 'auth/signup'){
            return redirect()->to('auth/signup/1');
        }
            
        if($step == 2){
            // dd($this->request->getCookie('__srvkit%3As1__'));
            if(!$this->request->getCookie('__srvkit%3As1__')){
                throw new PageNotFoundException('The page you\'re looking for isn\'t available.');
            }
        }
        return view('SrvKit\Auth\Views\auth', ['path' => $this->request->getPath()]);
    }

    public function signup($step = false){
        $session = Services::session();
        $session->set('message', 'my session value');
        $session->setFlashdata('message', 'my session value');
        dd($session);
        return $this->redirect()->back()->with('l5error', 'Some error encounterd');
        // d($step);
        // dd($this->request->getPost());
        if($step == 1) {
            $name = $this->request->getPost('name');
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $cookie = new Cookie('__srvkit:s1__', json_encode([
                'name' => $name,
                'username' => $username,
                'password' => $password
            ]), [
               "expire" => 3600,
               "httponly" => true,
               "secure" => false,
               "samesite" => 'Strict',
               "path"=> '/'
            ]);
            $this->response->setCookie($cookie);
            return $this->response->redirect('/auth/signup/2');
        }

        // Finish
        if($step == 2) {
            // Save this to database
            $email = $this->request->getPost('email');
            $requestedRole = $this->request->getPost('role');

            // dd($this->request->getPost());

            $availableRole = ['memeber', 'auther', 'admin', 'owner'];
            if(!in_array($requestedRole, $availableRole)){
                return redirect()->back()->with('message', 'Invalid role selected')->withInput();
            }

            try {
                $step1data = $this->request->getCookie('__srvkit%3As1__');
                if(!$step1data) return redirect()->to('auth/signup/1');
                $row = json_decode($step1data, true);
                $userModel = new UserModel();
                $row['email'] = $email;
                $row['role'] = $requestedRole;
                !in_array($requestedRole, ['auther', 'admin', 'owner']) ?: $row['requested_role'] = $requestedRole;
                $userModel->insert($row);
                return redirect()->to('auth/login?message=new-user-created-successfully')->with('message', 'New user created successfully.');
            } catch (Exception $e) {
                $cookie = new Cookie('__srvkit%3As2__', json_encode($row), [
                    "expire" => 3600,
                    "httponly" => true,
                    "secure" => false,
                    "samesite" => 'Strict',
                    "path"=> '/'
                ]);
                $this->response->setCookie();
                return redirect()->back()->with('message', 'Invalid role selected')->withInput();
                
            }
        }

        return redirect()->to('auth/signup/xhttp');
    }

    public function login(){
        try {
             $auth = Services::auth();
             $payload = (object) $this->request->getPost();
             $username = $payload->username;
             $password = $payload->password;
             $accessToken = $auth->login($username, $password)->generateAccessToken();

             $refreshToken = $auth->generateRefreshToken();

             $cookie = new Cookie('__refresh_token__', $refreshToken, [
                "expire" => 3600,
                "httponly" => true,
                "secure" => false,
                "samesite" => 'Strict',
                "path"=> '/'
             ]);

             // Set cookie for refresh token
             $this->response->setCookie($cookie);

             return $this->respond([
                "message" => "Login Successful.",
                "user" => $auth->getUser(),
                "status" => "success",
                "__access_token__" => $accessToken,
                "redirect" => "redirectURL"
             ], 200, 'Authentication Successful'); 
        } catch (AuthException $e) {
            return $this->fail($e->getMessage(), code: $e->getCode(), customMessage: 'Authentication Error');
        }
    }

    public function test(){
        $userModel = new UserModel();

        // dd($userModel->select('*')->findAll());

       /* $save = $userModel->save([
            'name' => 'Rahul Sharma',
            'username' => 'rahul2005',
            'password' => 'secret123',
            'email' => 'rahul@email.com',
            'role' => 'memeber',
            'requested_role' => 'author',
            // 'requested_role_date' => new DateTime()
        ]);*/

        if (!$userModel->save([
            'name' => 'Rahul Sharma',
            'username' => 'rahul2005',
            'password' => 'secret123',
            'email' => 'rahul@email.com',
            'role' => 'member',
            'requested_role' => 'author',
        ])) {
            dd($userModel->errors());
        }

        // dd($save);
    }
}
