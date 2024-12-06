<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\Contracts\SessionInterface;
use App\Entity\StockMovement;
use App\ResponseFormatter;
use App\Services\RequestService;
use App\Services\StockService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class StockController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly StockService $stockService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session
    ) {}

    public function index(Response $response): Response
    {
        return $this->twig->render($response, 'stock_movements/index.twig');
    }

    public function get(Response $response, StockMovement $stockMovement): Response
    {
        $data = [
            'id' => $stockMovement->getId(),
            'product' => $stockMovement->getProduct()->getName(),
            'quantity' => $stockMovement->getQuantity(),
            'movementType' => $stockMovement->getMovementType(),
            'createdAt' => $stockMovement->getCreatedAt()->format('m/d/Y g:i A'),
        ];

        return $this->responseFormatter->asJson($response, $data);
    }


    public function load(Request $request, Response $response): Response
    {
        $params = $this->requestService->getDataTableQueryParameters($request);
        $products = $this->stockService->getPaginatedProducts($params);
        $transformer = function (StockMovement $product) {
            return [
                'id' => $product->getId(),
                'product' => $product->getProduct()->getName(),
                'quantity' => $product->getQuantity(),
                'movementType' => $product->getMovementType(),
                'createdAt' => $product->getCreatedAt()->format('m/d/Y g:i A'),
            ];
        };

        $totalProducts = count($products);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array)$products->getIterator()),
            $params->draw,
            $totalProducts
        );
    }
}
