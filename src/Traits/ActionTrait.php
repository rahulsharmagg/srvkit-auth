<?php
 
namespace SrvKit\Auth\Traits;

use Exception;
use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Models\UserModel;

trait ActionTrait {
	protected String $action;
	public function action(string $action): self
	{
	    try {
	        if(!in_array($action, $this->config->actions)){
	            throw new AuthException('Invalid Action.');
	        }
	        $this->action = $action;
	        return $this;
	    } catch (Exception $e) {
	        throw new AuthException($e->getMessage());
	    }
	}

	/**
	 * Render the view of the action
	 * @param  string $action [description]
	 * @return [type]         [description]
	 */
	public function view(string $action = '', array $data = [])
	{
	    if(empty($action)){
	        $action = $this->action;
	    }
	    return view($this->config->views['action'], ['action' => $action, ...$data]);
	}
}
