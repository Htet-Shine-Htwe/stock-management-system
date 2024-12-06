<?php

namespace App\Services;

use App\Contracts\EntityManagerServiceInterface;
use App\Entity\Product;

class ProductAvailabilityService
{
    private readonly EntityManagerServiceInterface $entityManagerService;

    public function __construct(EntityManagerServiceInterface $entityManagerService)
    {
        $this->entityManagerService = $entityManagerService;
    }

    public function checkAvailability(array $products): mixed
    {
        foreach($products as $product) {
            $productEntity = $this->entityManagerService->getRepository(Product::class)->find($product['id']);
            if($productEntity->getStockQuantity() < $product['quantity']) {
                return [
                    'status' => false,
                    'product' => $productEntity->getName()
                ];
            }
        }

        return [
            'status' => true
        ];
    }
}
