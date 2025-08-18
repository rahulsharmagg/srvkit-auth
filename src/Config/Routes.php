<?php
namespace SrvKit\Auth\Config;

use CodeIgniter\Router\RouteCollection;

/** @var Auth */
$authConfig = config('Auth');
if($authConfig->routeEnabled):

/**
 * @var RouteCollection $routes
 */
$routes->group('auth', ['namespace' => 'SrvKit\Auth\Controllers', 'filter' => 'cors:api'], function ($routes) {
    $routes->addPlaceholder('step', '[1-2]{1}');
    $routes->get('', 'AuthController::auth');
    $routes->get('ping', 'AuthController::ping');
    $routes->get('test', 'AuthController::test');
    $routes->get('login', 'LoginController::index');
    $routes->get('verify', '', ['filter' => 'verifytemp']);
    $routes->get('signup/(:step)', 'SignupController::index/$1');
    $routes->get('check-username', 'SignupController::checkUserName');
    $routes->get('action', 'ActionController::index/$1');
    $routes->get('action/(:segment)', 'ActionController::index/$1');
    $routes->post('login', 'LoginController::login');
    $routes->post('loginx', 'LoginController::asyncLogin');
    $routes->post('refresh', 'ActionController::refreshToken');
    $routes->post('signup', 'SignupController::signup');
    $routes->post('signup/(:step)', 'SignupController::signup/$1');
    $routes->post('signup/cancel', 'SignupController::cancel');
    $routes->post('action/(:segment)', 'ActionController::action/$1');
    $routes->post('check-username', 'SignupController::checkUserName');
    $routes->post('ping', 'AuthController::ping');
    $routes->put('action/(:segment)', 'ActionController::action/$1', ['filter' => 'verifytemp']);
});

$routes->options('auth/(:any)', static function () {});

$routes->group('user', ['namespace' => 'SrvKit\Auth\Controllers\Dashboard', 'filter' => ['loggedin']], function ($routes) {
/** @var RouteCollection $routes */
    $routes->get('(:segment)', 'DashboardController::index/$1', ['as' => 'dashboard']);
    $routes->get('(:segment)/profile', 'DashboardController::profile/$1', ['as' => 'profile']);
    $routes->get('(:setment)/profile/settings', 'DashboardController::settings/$1', ['as' => 'settings']);
});

endif;
