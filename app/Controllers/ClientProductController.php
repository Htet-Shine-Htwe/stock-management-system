<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\Contracts\SessionInterface;
use App\Entity\Product;

use App\ResponseFormatter;
use App\Services\ProductService;
use App\Services\RequestService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class ClientProductController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly ProductService $productService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session
    ) {}

    public function index(Response $response): Response
    {
        return $this->twig->render($response, 'client-products/index.twig');
    }


    public function get(Response $response, Product $product): Response
    {
        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'stockQuantity' => $product->getStockQuantity()
        ];

        return $this->responseFormatter->asJson($response, $data);
    }

    public function load(Request $request, Response $response): Response
    {
        $params = $this->requestService->getDataTableQueryParameters($request);
        $products = $this->productService->getPaginatedProducts($params);
        $transformer = function (Product $product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'stockQuantity' => $product->getStockQuantity(),
                'category' => $product->getCategory()->getName(),
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

    public function addStock(Request $request, Response $response, Product $product): Response
    {
        $data = $request->getParsedBody();

        $this->productService->addStock($product, (int) $data['quantity']);

        return $this->responseFormatter->asJson($response, [
            'message' => 'Stock added successfully',
            'stockQuantity' => $product->getStockQuantity(),
            'data' => $data
        ]);
    }
}
