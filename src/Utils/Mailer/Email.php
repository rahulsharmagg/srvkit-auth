<?php

namespace SrvKit\Auth\Utils\Mailer;


class Email{
	/**
	 * Sends reset password url email to given email-address
	 * @param string $addr [description]
	 */
	public static function sendResetPasswordRequest(string $addr, string $url){
		$email = \Config\Services::email();
		$email->setFrom('test@codeblaze.in', 'SrvKit Authentication');
		$email->setTo($addr);
        $email->setHeader('X-Priority', '3');
        $date = date('d/m/Y');
        $email->setSubject('Password Reset Request '.$date. ' ['.uniqid().']');
        // Set headers for categorization
        $email->setHeader('List-ID', 'password-reset.example.com');
        $email->setHeader('Precedence', 'bulk');
        $message = view('\SrvKit\Auth\Views\emails\reset-password', ['reset_url' => $url]);
        $email->setMessage($message);
        $error_ = false;
        if(!$email->send()){
        	$error_ = $email->printDebugger(['headers']);
	        return $error_;
        }

        return true;
	}

	/**
	 * Send email to user's email if login attempt success
	 * @param  string $addr [description]
	 * @return [type]       [description]
	 */
	public static function sendLoginReport(string $addr, array $data){
		$email = \Config\Services::email();
		$client = \Config\Services::curlrequest();
		$_data = function () use($data, $client) {
			$ip = isset($data['ip_address']) ? $data['ip_address'] : 'n/a';
			$location = $ip === '::1' ? 'self' : 'n/a';
			$response = $client->get('http://ip-api.com/json/'.$ip, [
				'timeout' => 10,
	            'http_errors' => false
			]);

			if($response->getStatusCode() === 200){
				$body = json_decode($response->getBody(), true);
				if($body["status"] === "success"){
					$location = $body['city'] .'('. $body['regionName'] .'), '. $body['country'];
				}
			}


			return [
				'ipAddress' => $ip,
				'location' => $location,
				'device' => $data['user_agent'] ?? 'n/a',
				'loginTime' => ($data['logged_in_at'] ?? date('c')).' UTC',
				'secureAccountUrl' => $data['url'] ?? 'n/a'
			];
		};

		if(!empty($addr)){
			$email->setFrom('test@codeblaze.in', 'SrvKit Authentication');
			$email->setTo($addr);
	        $email->setHeader('X-Priority', '3');
	        $date = date('d/m/Y');
	        $email->setSubject('New sign-in to your account');
	        // Set headers for categorization
	        $email->setHeader('List-ID', 'login-alerts.codeblaze.in');
	        $email->setHeader('Precedence', 'bulk');
	        $message = view('\SrvKit\Auth\Views\emails\login-alert', $_data());
	        $email->setMessage($message);
	        $email->send();
			return true;
		}
		return false;
	}
}