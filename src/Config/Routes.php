<?php
namespace SrvKit\Auth\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('auth', ['namespace' => 'SrvKit\Auth\Controllers'], function ($routes) {
    $routes->addPlaceholder('step', '[1-2]{1}');
    $routes->get('', 'AuthController::index');
    $routes->get('refresh', 'AuthController::refresh');
    $routes->get('login', 'LoginController::index');
    $routes->get('signup/(:step)', 'SignupController::index/$1');
    $routes->get('verify', 'AuthController::verify', ['filter' => 'verifytemp']);
    $routes->post('login', 'LoginController::login');
    $routes->post('signup', 'SignupController::signup');
    $routes->post('signup/(:step)', 'SignupController::signup/$1');
    $routes->post('signup/cancel', 'SignupController::cancel');
    $routes->post('signup/cancel', 'SignupController::cancel');    
    $routes->post('action/(:segment)', 'AuthController::action/$1');
    $routes->put('action/(:segment)', 'AuthController::action/$1', ['filter' => 'verifytemp']);
    $routes->get('test', 'AuthController::test');
});
