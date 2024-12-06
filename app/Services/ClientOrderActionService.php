<?php

namespace App\Services;

use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ClientOrderActionService
{
    private readonly ProductAvailabilityService $productAvailabilityService;
    private readonly OrderCreationService $orderCreationService;
    private readonly StockUpdateService $stockUpdateService;
    private readonly StockMovementService $stockMovementService;

    public function __construct(
        ProductAvailabilityService $productAvailabilityService,
        OrderCreationService $orderCreationService,
        StockUpdateService $stockUpdateService,
        StockMovementService $stockMovementService,
        private readonly EntityManagerInterface $emm,  
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session
    ) {
        $this->productAvailabilityService = $productAvailabilityService;
        $this->orderCreationService = $orderCreationService;
        $this->stockUpdateService = $stockUpdateService;
        $this->stockMovementService = $stockMovementService;
    }

    public function order(Request $request)
    {
        // Start transaction
        $this->emm->beginTransaction();
        try {
            $products = $request->getParsedBody()['items'];
            $userId = $this->session->get('user');  

            $checkAvailability = $this->productAvailabilityService->checkAvailability($products);
            if (!$checkAvailability['status']) {
                throw new Exception('Product ' . $checkAvailability['product'] . ' is out of stock');
            }   
            $order = $this->orderCreationService->createOrder($products, $userId);

            $this->stockUpdateService->updateStock($products);

            $this->stockMovementService->recordStockMovement($products);

            $this->emm->commit();
            // Return created order
            return $order;

        } catch (Exception $e) {
            $this->emm->rollback();
            throw $e;
        }
    }
}