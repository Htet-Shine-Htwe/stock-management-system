<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EntityManagerServiceInterface;
use App\DataObjects\DataTableQueryParams;
use App\Entity\Order;
use App\Entity\StockMovement;
use Doctrine\ORM\Tools\Pagination\Paginator;

class OrderService
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManager)
    {
    }

    public function getPaginatedProducts(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManager
            ->getRepository(Order::class)
            ->createQueryBuilder('p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        $orderBy = in_array($params->orderBy, ['id', 'user_id', 'order_date', 'total_amount','status']) ? $params->orderBy : 'order_date';
        $orderDir = strtolower($params->orderDir) == 'asc' ? 'asc' : 'desc';

        if (!empty($params->searchTerm)) {
            $query->where('p.id LIKE :name')->setParameter(
                'id',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }

        $query->orderBy('p.' . $orderBy, $orderDir);

        return new Paginator($query);
    }

    public function getById(int $id): ?Order
    {
        return $this->entityManager->find(Order::class, $id);
    }

}
