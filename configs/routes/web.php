<?php

declare(strict_types=1);

use App\Controllers\AuthController;

use App\Controllers\ProductController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // Routes for guests (login page)
    $app->group('', function (RouteCollectorProxy $route) {
        $route->get('/login', [AuthController::class, 'loginView']);
        $route->post('/login', [AuthController::class, 'login']);
    })->add(GuestMiddleware::class);

    // Logout route
    $app->post('/logout', [AuthController::class, 'logout'])->add(AuthMiddleware::class);

    // Routes for admin and user roles
    $app->group('/admin', function (RouteCollectorProxy $group) {
       
        $group->group('/products',function (RouteCollectorProxy $products){
            $products->get('', [ProductController::class, 'index']);
            $products->get('/create', [ProductController::class, 'create']);
            $products->get("/load",[ProductController::class,'load']);
            $products->delete('/delete/{product}', [ProductController::class, 'delete']);
        });

    });

    $app->group('/user', function (RouteCollectorProxy $group) {
    });

};
