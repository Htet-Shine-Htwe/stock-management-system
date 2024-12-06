<?php

namespace App\Services;

use App\Contracts\EntityManagerServiceInterface;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Contracts\SessionInterface;
use App\Entity\User;

class OrderCreationService
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly SessionInterface $session
    ) {}

    public function createOrder(array $orderData, int $userId): Order
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
    
        if (!$user) {
            throw new \Exception('User not found');
        }
    
        $order = new Order();
        $order->setUser($user); 
        $order->setOrderDate(new \DateTime());
        $order->setTotalAmount(0.0); 
        $order->setStatus('PENDING'); 


        $this->entityManager->persist($order);
        $this->entityManager->flush();  
    
        foreach ($orderData as $itemData) {
            $product = $this->entityManager->getRepository(Product::class)->find($itemData['id']);
            if (!$product) {
                throw new \Exception('Product not found');
            }

    
            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setQuantity($itemData['quantity']);
            $orderItem->setPrice($product->getPrice());
            $orderItem->setOrder($order); // Set the order for the orderItem
    
            $order->addOrderItem($orderItem);
    
            $this->entityManager->persist($orderItem);
            $this->entityManager->flush();
        }
    
        $order->calculateTotalAmount();
    
        // Flush all the changes (order and order items) to the database
        $this->entityManager->flush();
    
        return $order;
    }
    
}
