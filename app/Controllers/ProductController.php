<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\Contracts\SessionInterface;
use App\Entity\Category;
use App\Entity\Product;
use App\Exception\ValidationException;
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
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session
    ) {}

    public function index(Response $response): Response
    {
        return $this->twig->render($response, 'products/index.twig');
    }

    public function create(Response $response): Response
    {
        $categories = $this->entityManagerService->getRepository(Category::class)->findAll();

        $this->session->flash('alert',['type'=>'info','message'=>'You are in create product page']);

        return $this->twig->render($response, 'products/action.twig', [
            'categories' => $categories
        ]);
    }

    public function edit(Response $response, Product $product): Response
    {
        $categories = $this->entityManagerService->getRepository(Category::class)->findAll();
        return $this->twig->render($response, 'products/action.twig', [
            'product' => $product,
            'categories' => $categories
        ]);
    }
    public function store(Request $request, Response $response): Response
    {

        $data = $this->requestValidatorFactory->make(CreateProductRequestValidator::class)->validate(
            $request->getParsedBody()
        );
        $data['price'] = (float) $data['price'];
        $data['stock_quantity'] = (int) $data['stock_quantity'];

        $category = $this->entityManagerService->find(Category::class, $data['category']);
        
        $product = $this->productService->create($data['name'],$data['description'],$category,$data['price'],$data['stock_quantity']
        );

        $this->entityManagerService->sync($product);

        return $response->withHeader('Location', '/admin/products')->withStatus(302);
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
        $data['price'] = (float) $data['price'];
        $data['stock_quantity'] = (int) $data['stock_quantity'];

        $category = $this->entityManagerService->find(Category::class, $data['category']);

        $this->entityManagerService->sync(
            $this->productService->update(
                $product,
                $data['name'],
                $data['description'],
                $data['price'],
                $data['stock_quantity'],
                $category   
            )
        );

        return $response->withHeader('Location', '/admin/products')->withStatus(302);
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
