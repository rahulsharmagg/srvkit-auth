<?php 
namespace SrvKit\Auth\Controllers\Dashboard;

use SrvKit\Auth\Controllers\BaseController;
use SrvKit\Auth\Entities\User;

class DashboardController extends BaseController{
	protected User $user;

	public function __construct(){
	}

	public function index(string $username)
    {
		$this->user = $this->session->get('user');
    	$this->session->remove('user');		
    	$data = ['user' => $this->user];
        return view($this->config->views['dashboard'], $data);
    }

	public function profile(string $username)
	{
		$this->user = $this->session->get('user');
		$data = ['user' => $this->user];
		return view($this->config->views['dashboard'], $data);
	}
	public function settings(string $username)
	{
		$this->user = $this->session->get('user');
		$data = ['user' => $this->user];
		return view($this->config->views['dashboard'], $data);
	}
}


 ?>