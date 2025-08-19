<?php
namespace SrvKit\Auth\Config;
use CodeIgniter\Router\RouteCollection;

class APIRoute{
    /**
     * Create API Route for Authentication
     * @param  RouteCollection $routes
     * @param  array           $options
     * @return void                   
     */
    public static function create(RouteCollection $routes, array $options = []): void
    {
        $name = isset($options['name']) ? $options['name'] : 'api';
        $routes->group($name, ['namespace' => 'SrvKit\Auth\Controllers\Api'], static function ($routes) {
            $routes->get('', 'Auth::index', ['as' => 'api_home']);
            $routes->match(['get', 'post'], 'test', 'Auth::test');
            $routes->post('auth/login', 'Auth::login', ['as' => 'api_login']);
            $routes->post('auth/signup', 'Auth::signup', ['as' => 'api_signup']);
            $routes->post('auth/check-username', 'Auth::getUsernameStatus');
            $routes->post('auth/reset-password', 'Auth::resetPassword');
            $routes->match(['GET', 'POST', 'PUT', 'DELETE'], '(:any)', 'Auth::noResource');
        });
    }
}