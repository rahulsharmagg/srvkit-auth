<?php 
namespace SrvKit\Auth\Controllers\Dashboard;

use SrvKit\Auth\Controllers\BaseController;
use SrvKit\Auth\Entities\User;

class DashboardController extends BaseController{

	public function index(string $username)
    {
        return view($this->config->views['dashboard']);
    }

	public function profile(string $username)
	{
		return view($this->config->views['dashboard']);
	}
	public function settings(string $username)
	{
		return view($this->config->views['dashboard']);
	}
}


 ?>