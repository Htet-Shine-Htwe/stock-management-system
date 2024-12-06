<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EntityManagerServiceInterface;
use App\DataObjects\DataTableQueryParams;
use App\Entity\StockMovement;
use App\Entity\Product;
use Doctrine\ORM\Tools\Pagination\Paginator;

class StockService
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManager)
    {
    }

    public function getPaginatedProducts(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManager
            ->getRepository(StockMovement::class)
            ->createQueryBuilder('p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        $orderBy = in_array($params->orderBy, ['id', 'product_id', 'quantity', 'created_at']) ? $params->orderBy : 'created_at';
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

    public function getById(int $id): ?StockMovement
    {
        return $this->entityManager->find(StockMovement::class, $id);
    }

}
