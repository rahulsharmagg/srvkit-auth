<?php
 
namespace SrvKit\Auth\Traits;

use Exception;
use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Models\UserModel;

trait ActionTrait {
	public function resetPassword()
	{
		try {
			$username = $this->request->getPost('username');
			$email = $this->request->getPost('email');

			$userModel = new UserModel();

			/** @var ?User [description] */
			$user = $userModel->where('username', $username)->where('email', $email)->first();
			if($user) {
				$tempLink = Services::tempLink();
				$tempLink->for($user)->action('reset-password')->create();
				$tempLink->save();
				$link = $tempLink->link();
				// dd($link); // send email from here
				$message = 'An email has been successfully sent to "'.$user->email.'", check and follow the password recovery link';
				return redirect()->to('auth?for=forgot-password')->with('message', 'success:'.$message)->with('icon', 'success');
				// return view($this->config->views['action'], ['action' => 'message-block']);
			} else {
				throw new AuthException('Incorrect username or email');
			}

		} catch (AuthException $e) {
			return redirect()->back()->with('message', 'error:'.$e->getMessage())->withInput();
		}
	}

	public function updatePassword()
	{
		try {
			$tempLink = Services::tempLink();
			$tempLink->init($this->request->getPost('token'));

			$validationRule = [
			    'password' => [
			        'label' => 'New Password',
			        'rules' => 'required|min_length[6]|regex_match[/^(?=.*\d)(?=.*[a-z])(?=.*[\W_])(?!.*\s).+$/]',
			        'errors' => [
			            'required' => '{field} is required.',
			            'min_length' => 'The {field} must be at least 6 characters long.'
			        ]
			    ],
			    'passconf' => [
			        'label' => 'Confirm New Password',
			        'rules' => 'matches[password]',
			        'errors' => [
			            'matches' => 'The {field} is required & must match the new password.'
			        ]
			    ]
			];

			if (!$this->validate($validationRule)) {
				$errors = array_values($this->validator->getErrors());
				return redirect()->back()->withInput()->with('message', 'error:'.$errors[0]);
			}

			$newPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

			$userModel = new UserModel();
			$userModel->where('id', $tempLink->user->id)->set('password', $newPassword)->update();

			$tempLink->use();
			$message = 'Password Update Successfully.';
			return redirect()->to('auth?for=reset-password')->with('message', 'success:'.$message)->with('icon', 'success');
		} catch (AuthException $e) {
			throw $e;
		}
	}
}
