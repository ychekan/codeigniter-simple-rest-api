<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// API
$routes->group('api', function ($routes) {
    $routes->post('register', 'Api\AuthController::register');
    $routes->post('confirm-account', 'Api\AuthController::confirm');
    $routes->post('forgot-password', 'Api\AuthController::forgotPassword');
    $routes->post('reset-password', 'Api\AuthController::resetPassword');
    $routes->post('login', 'Api\AuthController::login');
    $routes->post('logout', 'Api\AuthController::logout', ['filter' => 'auth:api']);
    $routes->post('refresh', 'Api\AuthController::refresh', ['filter' => 'auth:api']);
    $routes->get('profile', 'Api\AuthController::profile', ['filter' => 'auth:api']);

    /* User crud */
    $routes->group('users', ['filter' => 'admin:api'], function ($routes) {
        $routes->get('view', 'Api\Users::show');
        $routes->post('store', 'Api\Users::store');
        $routes->post('update', 'Api\Users::update');
        $routes->post('delete', 'Api\Users::delete');
    });
});
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
