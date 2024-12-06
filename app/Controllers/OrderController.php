<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\Contracts\SessionInterface;
use App\Entity\Order;
use App\Entity\Product;

use App\ResponseFormatter;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\RequestService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class OrderController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly OrderService $orderService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session
    ) {}

    public function index(Response $response): Response
    {
        return $this->twig->render($response, 'orders/index.twig');
    }


    public function get(Response $response, Order $order): Response
    {
        $data = [
            'id' => $order->getId(),
            'user_name' => $order->getUser()->getName(),    
            'item_count' => $order->getOrderItems()->count(),
            'total_amount' => $order->getTotalAmount(),
            'order_date' => $order->getOrderDate()->format('m/d/Y g:i A'),
            'status' => $order->getStatus(),
        ];

        return $this->responseFormatter->asJson($response, $data);
    }


    public function load(Request $request, Response $response): Response
    {
        $params = $this->requestService->getDataTableQueryParameters($request);
        $orders = $this->orderService->getPaginatedProducts($params);
        $transformer = function (Order $order) {
            return [
                'id' => $order->getId(),
            'user_name' => $order->getUser()->getName(),    
            'item_count' => $order->getOrderItems()->count(),
            'total_amount' => $order->getTotalAmount(),
            'order_date' => $order->getOrderDate()->format('m/d/Y g:i A'),
            'status' => $order->getStatus(),
            ];
        };

        $totalOrders = count($orders);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array)$orders->getIterator()),
            $params->draw,
            $totalOrders
        );
    }

}
