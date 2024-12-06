<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\HasTimeStamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'orders')]
class Order
{
    use HasTimeStamp;

    #[ORM\Id, ORM\Column(type: 'integer', options: ['unsigned' => true]), ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $order_date;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $total_amount;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist'])]
    private Collection $orderItems;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status; // Add order status field

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    // Getters and Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getOrderDate(): \DateTime
    {
        return $this->order_date;
    }

    public function setOrderDate(\DateTime $orderDate): self
    {
        $this->order_date = $orderDate;
        return $this;
    }

    public function getTotalAmount(): float
    {
        return $this->total_amount;
    }

    public function setTotalAmount(float $totalAmount): self
    {
        $this->total_amount = $totalAmount;
        return $this;
    }

    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
        }
    
        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        $this->orderItems->removeElement($orderItem);
        return $this;
    }

    // New status getter and setter
    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    // Custom method to calculate the total amount
    public function calculateTotalAmount(): void
    {
        $total = 0;
        foreach ($this->orderItems as $item) {
            $total += $item->getPrice() * $item->getQuantity();
        }
        $this->total_amount = $total;
    }
}
