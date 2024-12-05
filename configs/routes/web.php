<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Middleware\AdminGuardMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\RoleGuardMiddleware;
use App\Middleware\UserGuardMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // Routes for guests (login page)
    $app->group('', function (RouteCollectorProxy $route) {
        $route->get('/login', [AuthController::class, 'loginView'])->add(GuestMiddleware::class);
        $route->post('/login', [AuthController::class, 'login'])->add(GuestMiddleware::class);
    })->add(GuestMiddleware::class);

    // Logout route
    $app->post('/logout', [AuthController::class, 'logout'])->add(AuthMiddleware::class);

    // Routes for admin and user roles
    $app->group('/admin', function (RouteCollectorProxy $group) {
       
    })->add(AdminGuardMiddleware::class); // Only admin has access to these routes

    $app->group('/user', function (RouteCollectorProxy $group) {
    })->add(UserGuardMiddleware::class); // Both admin and user can access

};
