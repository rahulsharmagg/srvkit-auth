<?php
namespace SrvKit\Auth\Services;

use Exception;
use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Entities\ActionToken;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Models\ActionTokenModel;

class TempLink
{
    /**
     * summary
     */
    protected ActionToken $token;
    protected String $action;
    public    User   $user;
    protected bool   $saved;
    protected Array  $_payload;

    public    String $tid;
    
    public function __construct()
    { /**/ }

    public function getAction(): string
    {
    	if(!isset($this->action)) return '';
    	return $this->action;
    }

    public function init(string $token): self
    {
        $actionTokenModel = new ActionTokenModel();
        $tokenHash = hash('sha256', $token);
        $token = $actionTokenModel->where('token_hash', $tokenHash)->first();
        if(!$token) throw new Exception('Token not found.');
        
        // Set inital value
        $this->token = $token;
        $this->action = $token->action;
        $this->user = $token->getUser();
        $this->saved = true;

        return $this;
    }

    public function verify(string $token)
    {
    	$actionTokenModel = new ActionTokenModel();
    	$tokenHash = hash('sha256', $token);
    	$token = $actionTokenModel->where('token_hash', $tokenHash)->first();
    	if(!$token) throw new Exception('Token not found.');
    	if($token->isExpired()) throw new Exception('Token is expired');
    	if($token->used) throw new Exception('Token is already used.');
    	$this->action = $token->action;
    	return $this;
    }

    public function for(User $user):self
    {
    	$this->user = $user;
    	return $this;
    }

    public function action(string $action): self
    {
    	$this->action = $action;
    	return $this;
    }

    public function use(): bool
    {
        $actionTokenModel = new ActionTokenModel();
        return $actionTokenModel->where('token_hash', $this->token->token_hash)
                                ->set('used', true)
                                ->set('used_at', date('Y-m-d H:i:s'))
                                ->update();
    }

    public function create(): self
    {
        $this->tid = bin2hex(random_bytes(32));
        $seconds = config(Auth::class)::TEMP_LINK_EXP;
        $expiresAt = date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds'));

        $this->_payload = [
        	'user_id' => $this->user->id,
        	'token_hash' => hash('sha256', $this->tid),
        	'action' => $this->action,
        	'ip_address' => $_SERVER['REMOTE_ADDR'],
        	'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        	'expires_at' => $expiresAt,
        	'created_at' => date('Y-m-d H:i:s')
        ];

        return $this;
    }


    public function save(): void
    {
    	$actionTokenModel = new ActionTokenModel();
    	if(!$actionTokenModel->save($this->_payload)){
    		[0 => $error] = $actionTokenModel->errors();
    		throw new Exception($error);
    	}
		$this->saved = true;
    }

    public function link(): null|string
    {
    	if($this->saved){
    		return base_url('/auth/verify?token='.$this->tid);
    	}

    	return null;
    }

}