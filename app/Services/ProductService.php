<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EntityManagerServiceInterface;
use App\DataObjects\DataTableQueryParams;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ProductService
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly StockMovementService $stockMovementService
    )
    {
    }

    public function create(string $name, string $description,Category $category, float $price, int $stockQuantity): Product
    {
        $product = new Product();

        return $this->update($product, $name, $description, $price, $stockQuantity,$category);
    }

    public function getPaginatedProducts(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManager
            ->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        $orderBy = in_array($params->orderBy, ['name', 'price', 'stockQuantity', 'createdAt','updatedAt']) ? $params->orderBy : 'createdAt';
        $orderDir = strtolower($params->orderDir) == 'asc' ? 'asc' : 'desc';

        if (!empty($params->searchTerm)) {
            $query->where('p.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }

        $query->orderBy('p.' . $orderBy, $orderDir);

        return new Paginator($query);
    }

    public function getById(int $id): ?Product
    {
        return $this->entityManager->find(Product::class, $id);
    }

    public function update(Product $product, string $name, string $description, float $price, int $stockQuantity,Category $category): Product
    {
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setCategory($category);
        $product->setStockQuantity($stockQuantity);
        $product->setCreatedAt(new \DateTime());
        $product->setUpdatedAt(new \DateTime());

        return $product;
    }

    public function delete(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    public function addStock(Product $product, int $quantity): Product
    {
        $product->setStockQuantity($product->getStockQuantity() + $quantity);
    
        $this->entityManager->persist($product);
        
        $this->entityManager->flush();

        $this->stockMovementService->createStockMovement($product, $quantity, 'IN');

        return $product;
    }
    
}
