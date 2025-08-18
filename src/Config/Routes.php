<?php
namespace SrvKit\Auth\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('auth', ['namespace' => 'SrvKit\Auth\Controllers'], function ($routes) {
    $routes->addPlaceholder('step', '[1-2]{1}');
    $routes->get('', 'AuthController::auth');
    $routes->get('test', 'AuthController::test');
    $routes->get('login', 'LoginController::index');
    $routes->get('verify', '', ['filter' => 'verifytemp']);
    $routes->get('signup/(:step)', 'SignupController::index/$1');
    $routes->get('action', 'ActionController::index/$1');
    $routes->get('action/(:segment)', 'ActionController::index/$1');
    $routes->post('login', 'LoginController::login');
    $routes->post('loginx', 'LoginController::asyncLogin');
    $routes->post('refresh', 'ActionController::refreshToken');
    $routes->post('signup', 'SignupController::signup');
    $routes->post('signup/(:step)', 'SignupController::signup/$1');
    $routes->post('signup/cancel', 'SignupController::cancel');
    $routes->post('action/(:segment)', 'ActionController::action/$1');
    $routes->put('action/(:segment)', 'ActionController::action/$1', ['filter' => 'verifytemp']);
});

/** @var RouteCollection $routes */
$routes->group('usr', ['namespace' => 'SrvKit\Auth\Controllers\Dashboard', 'filter' => ['user', 'loggedin']], function ($routes) {
    $routes->addPlaceholder('username', '^[a-zA-Z0-9\_].+');
    $routes->get('(:username)/profile', 'DashboardController::profile/$1', ['as' => 'profile']);
    $routes->get('(:username)/profile/settings', 'DashboardController::settings/$1', ['as' => 'settings']);

    // Catch all routes as dashboard
    $routes->get('(:username)', 'DashboardController::index/$1', ['as' => 'dashboard']);
});
