<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\Contracts\SessionInterface;
use App\Entity\Product;

use App\ResponseFormatter;
use App\Services\ClientOrderActionService;
use App\Services\ProductService;
use App\Services\RequestService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class ClientOrderController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly ClientOrderActionService $clientOrderActionService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session
    ) {}

    public function order(Request $request, Response $response): Response
    {
        $userId = $this->getUserIdFromSession();  

        $orderData = $request->getParsedBody();

        $order = $this->clientOrderActionService->order($request);

        return $this->responseFormatter->asJson($response, $order);
    }

    private function getUserIdFromSession(): int
    {
        $user = $this->session->get('user');

        return $user ?? 0; 
    }

    
}
