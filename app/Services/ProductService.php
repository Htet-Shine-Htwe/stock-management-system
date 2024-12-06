<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EntityManagerServiceInterface;
use App\DataObjects\DataTableQueryParams;
use App\Entity\Product;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ProductService
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManager)
    {
    }

    public function create(string $name, string $description, float $price, int $stockQuantity): Product
    {
        $product = new Product();

        return $this->update($product, $name, $description, $price, $stockQuantity);
    }

    public function getPaginatedProducts(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManager
            ->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        $orderBy = in_array($params->orderBy, ['name', 'price', 'stockQuantity', 'updatedAt']) ? $params->orderBy : 'updatedAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

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

    public function update(Product $product, string $name, string $description, float $price, int $stockQuantity): Product
    {
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setStockQuantity($stockQuantity);
        $product->setUpdatedAt(new \DateTime());

        return $product;
    }

    public function delete(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}
