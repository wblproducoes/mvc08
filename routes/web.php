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

// Rotas de Usuários (CRUD)
$router->get('/users', 'UserController', 'index')
       ->middleware('AuthMiddleware');

$router->get('/users/trash', 'UserController', 'trash')
       ->middleware('AuthMiddleware');

$router->get('/users/create', 'UserController', 'create')
       ->middleware('AuthMiddleware');

$router->post('/users', 'UserController', 'store')
       ->middleware('AuthMiddleware');

$router->get('/users/{id}/edit', 'UserController', 'edit')
       ->middleware('AuthMiddleware');

$router->put('/users/{id}', 'UserController', 'update')
       ->middleware('AuthMiddleware');

$router->delete('/users/{id}', 'UserController', 'destroy')
       ->middleware('AuthMiddleware');

$router->post('/users/{id}/restore', 'UserController', 'restore')
       ->middleware('AuthMiddleware');

$router->post('/users/{id}/force-delete', 'UserController', 'forceDelete')
       ->middleware('AuthMiddleware');

// Rotas de Status (CRUD)
$router->get('/status', 'StatusController', 'index')
       ->middleware('AuthMiddleware');

$router->get('/status/trash', 'StatusController', 'trash')
       ->middleware('AuthMiddleware');

$router->get('/status/create', 'StatusController', 'create')
       ->middleware('AuthMiddleware');

$router->post('/status', 'StatusController', 'store')
       ->middleware('AuthMiddleware');

$router->get('/status/{id}/edit', 'StatusController', 'edit')
       ->middleware('AuthMiddleware');

$router->put('/status/{id}', 'StatusController', 'update')
       ->middleware('AuthMiddleware');

$router->delete('/status/{id}', 'StatusController', 'destroy')
       ->middleware('AuthMiddleware');

$router->post('/status/{id}/restore', 'StatusController', 'restore')
       ->middleware('AuthMiddleware');

$router->post('/status/{id}/force-delete', 'StatusController', 'forceDelete')
       ->middleware('AuthMiddleware');

// Rotas de Níveis (CRUD)
$router->get('/levels', 'LevelController', 'index')
       ->middleware('AuthMiddleware');

$router->get('/levels/trash', 'LevelController', 'trash')
       ->middleware('AuthMiddleware');

$router->get('/levels/create', 'LevelController', 'create')
       ->middleware('AuthMiddleware');

$router->post('/levels', 'LevelController', 'store')
       ->middleware('AuthMiddleware');

$router->get('/levels/{id}/edit', 'LevelController', 'edit')
       ->middleware('AuthMiddleware');

$router->put('/levels/{id}', 'LevelController', 'update')
       ->middleware('AuthMiddleware');

$router->delete('/levels/{id}', 'LevelController', 'destroy')
       ->middleware('AuthMiddleware');

$router->post('/levels/{id}/restore', 'LevelController', 'restore')
       ->middleware('AuthMiddleware');

$router->post('/levels/{id}/force-delete', 'LevelController', 'forceDelete')
       ->middleware('AuthMiddleware');

// Rotas de Gêneros (CRUD)
$router->get('/genders', 'GenderController', 'index')
       ->middleware('AuthMiddleware');

$router->get('/genders/trash', 'GenderController', 'trash')
       ->middleware('AuthMiddleware');

$router->get('/genders/create', 'GenderController', 'create')
       ->middleware('AuthMiddleware');

$router->post('/genders', 'GenderController', 'store')
       ->middleware('AuthMiddleware');

$router->get('/genders/{id}/edit', 'GenderController', 'edit')
       ->middleware('AuthMiddleware');

$router->put('/genders/{id}', 'GenderController', 'update')
       ->middleware('AuthMiddleware');

$router->delete('/genders/{id}', 'GenderController', 'destroy')
       ->middleware('AuthMiddleware');

$router->post('/genders/{id}/restore', 'GenderController', 'restore')
       ->middleware('AuthMiddleware');

$router->post('/genders/{id}/force-delete', 'GenderController', 'forceDelete')
       ->middleware('AuthMiddleware');

// Rotas de Logs de Acesso (Read-only)
$router->get('/user-access-logs', 'UserAccessLogController', 'index')
       ->middleware('AuthMiddleware');

$router->get('/user-access-logs/{id}', 'UserAccessLogController', 'show')
       ->middleware('AuthMiddleware');
