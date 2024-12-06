<?php

namespace App\Services;

use App\Contracts\EntityManagerServiceInterface;
use App\Entity\Product;

class StockUpdateService
{
    private readonly EntityManagerServiceInterface $entityManagerService;

    public function __construct(EntityManagerServiceInterface $entityManagerService)
    {
        $this->entityManagerService = $entityManagerService;
    }

    public function updateStock(array $products)
    {
        foreach ($products as $product) {
            $productEntity = $this->entityManagerService->getRepository(Product::class)->find($product['id']);
            $newQuantity = $productEntity->getStockQuantity() - $product['quantity'];
            $productEntity->setStockQuantity($newQuantity);

            $this->entityManagerService->persist($productEntity);
        }

        // Commit transaction
        $this->entityManagerService->flush();
    }
}
