<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ClientOrderController;
use App\Controllers\ClientProductController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\StockController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\UserMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // Routes for guests (login page)
    $app->group('', function (RouteCollectorProxy $route) {
        $route->get('/login', [AuthController::class, 'loginView']);
        $route->post('/login', [AuthController::class, 'login']);
    })->add(GuestMiddleware::class);

    $app->post('/logout', [AuthController::class, 'logout'])->add(AuthMiddleware::class);

    // --------------------------------------------------- ADMIN ROUTES ---------------------------------------------------  //
    $app->group('/admin', function (RouteCollectorProxy $group) {

        $group->group('/products', function (RouteCollectorProxy $products) {
            $products->get('', [ProductController::class, 'index']);
            $products->get('/create', [ProductController::class, 'create']);
            $products->get('/edit/{product}', [ProductController::class, 'edit']);
            $products->post('/store', [ProductController::class, 'store']);
            $products->post('/update/{product}', [ProductController::class, 'update']);
            $products->get("/load", [ProductController::class, 'load']);
            $products->delete('/delete/{product}', [ProductController::class, 'delete']);
            $products->post("/add-stock/{product}", [ProductController::class, 'addStock']);
        });

        $group->group("/orders", function (RouteCollectorProxy $orders) {
            $orders->get('', [OrderController::class, 'index']);
            $orders->get('/load', [OrderController::class, 'load']);
        });

        $group->group("/stocks", function (RouteCollectorProxy $stock) {
            $stock->get('', [StockController::class, 'index']);
            $stock->get('/get/{stockMovement}', [StockController::class, 'get']);
            $stock->get('/load', [StockController::class, 'load']);
        });
    })->add(AuthMiddleware::class)->add(AdminMiddleware::class);

    // --------------------------------------------------- USER ROUTES ---------------------------------------------------  //
    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/products', [ClientProductController::class, 'index']);
        $group->get('/products/load', [ClientProductController::class, 'load']);

        $group->post('/purchase/order', [ClientOrderController::class, 'order']);
    })
    ->add(AuthMiddleware::class)->add(UserMiddleware::class);
};
