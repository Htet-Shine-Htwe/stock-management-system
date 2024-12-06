<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\Entity\Product;
use App\RequestValidators\CreateProductRequestValidator;
use App\RequestValidators\UpdateProductRequestValidator;
use App\ResponseFormatter;
use App\Services\ProductService;
use App\Services\RequestService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class ProductController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly ProductService $productService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
        private readonly EntityManagerServiceInterface $entityManagerService
    ) {
    }

    public function index(Response $response): Response
    {
        return $this->twig->render($response, 'products/index.twig');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(CreateProductRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $product = $this->productService->create($data['name'], $data['description'], $data['price'], $data['stock_quantity']);

        $this->entityManagerService->sync($product);

        return $response->withHeader('Location', '/products')->withStatus(302);
    }

    public function delete(Response $response, Product $product): Response
    {
        $this->entityManagerService->delete($product, true);

        return $response;
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

    public function update(Request $request, Response $response, Product $product): Response
    {
        $data = $this->requestValidatorFactory->make(UpdateProductRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->entityManagerService->sync($this->productService->update(
            $product,
            $data['name'],
            $data['description'],
            $data['price'],
            $data['stock_quantity']
        ));

        return $response;
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
}
