<?php

namespace App\Services;

use App\Contracts\EntityManagerServiceInterface;
use App\Entity\Product;
use App\Entity\StockMovement;

class StockMovementService
{
    public function __construct ( private readonly EntityManagerServiceInterface $entityManagerService)
    {
    }

    public function createStockMovement(Product $product, int $quantity, string $movementType): StockMovement
    {
        // Create a new stock movement entity
        $stockMovement = new StockMovement(); 
        $stockMovement->setProduct($product);
        $stockMovement->setQuantity($quantity);
        $stockMovement->setMovementType($movementType);

        $this->entityManagerService->sync($stockMovement);

        return $stockMovement;
    }
}