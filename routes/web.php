<?php

/**
 * Rotas web
 */

// Rotas públicas (guest)
$router->get('/', 'AuthController', 'showLogin')
       ->middleware('GuestMiddleware');

$router->get('/login', 'AuthController', 'showLogin')
       ->middleware('GuestMiddleware');

$router->post('/login', 'AuthController', 'login')
       ->middleware('GuestMiddleware');

$router->get('/forgot-password', 'AuthController', 'showForgotPassword')
       ->middleware('GuestMiddleware');

$router->post('/forgot-password', 'AuthController', 'forgotPassword')
       ->middleware('GuestMiddleware');

// Rotas autenticadas
$router->get('/dashboard', 'DashboardController', 'index')
       ->middleware('AuthMiddleware');

$router->get('/logout', 'AuthController', 'logout')
       ->middleware('AuthMiddleware');
