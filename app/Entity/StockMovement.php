<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\HasTimeStamp;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'stock_movements')]
class StockMovement
{
    #[ORM\Id, ORM\Column(type: 'integer', options: ['unsigned' => true]), ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $quantity;

    #[ORM\Column(type: 'string', length: 3)]
    private string $movement_type; // "IN" or "OUT"

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getMovementType(): string
    {
        return $this->movement_type;
    }

    public function setMovementType(string $movementType): self
    {
        if (!in_array($movementType, ['IN', 'OUT'], true)) {
            throw new \InvalidArgumentException('Invalid movement type. Allowed values are "IN" or "OUT".');
        }
        $this->movement_type = $movementType;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->created_at = $createdAt;

        return $this;
    }
}
