<?php
namespace SrvKit\Auth\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('auth', ['namespace' => 'SrvKit\Auth\Controllers'], function ($routes) {
    $routes->addPlaceholder('step', '[1-2]{1}');
    $routes->get('', 'AuthController::index');
    $routes->get('login', 'LoginController::index');
    $routes->get('signup/(:step)', 'SignupController::index/$1');
    $routes->post('login', 'LoginController::login');
    $routes->post('signup', 'SignupController::signup');
    $routes->post('signup/(:step)', 'SignupController::signup/$1');
    $routes->post('signup/cancel', 'SignupController::cancel');
    $routes->get('test', 'AuthController::test');
});