<?php

namespace Tests\Unit\Services;

use App\Contracts\EntityManagerServiceInterface;
use PHPUnit\Framework\TestCase;
use App\Services\ClientOrderActionService;
use App\Services\ProductAvailabilityService;
use App\Services\OrderCreationService;
use App\Services\StockUpdateService;
use App\Services\StockMovementService;
use Doctrine\ORM\EntityManagerInterface;
use App\Contracts\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

class ClientOrderActionServiceTest extends TestCase
{
    private ClientOrderActionService $clientOrderActionService;
    private MockObject $productAvailabilityService;
    private MockObject $orderCreationService;
    private MockObject $stockUpdateService;
    private MockObject $stockMovementService;
    private MockObject $entityManager;
    private MockObject $session;
    private MockObject $request;

    protected function setUp(): void
    {
        // Mock dependencies
        $this->productAvailabilityService = $this->createMock(ProductAvailabilityService::class);
        $this->orderCreationService = $this->createMock(OrderCreationService::class);
        $this->stockUpdateService = $this->createMock(StockUpdateService::class);
        $this->stockMovementService = $this->createMock(StockMovementService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->session = $this->createMock(SessionInterface::class);
        $this->request = $this->createMock(Request::class);

        // Initialize ClientOrderActionService with mocks
        $this->clientOrderActionService = new ClientOrderActionService(
            $this->productAvailabilityService,
            $this->orderCreationService,
            $this->stockUpdateService,
            $this->stockMovementService,
            $this->entityManager,
            $this->createMock(EntityManagerServiceInterface::class),
            $this->session
        );
    }

    public function testOrderSuccess(): void
    {
        $this->session->method('get')->willReturn(123);

        $this->request->method('getParsedBody')->willReturn([
            'items' => [
                ['id' => 1, 'quantity' => 2],
                ['id' => 2, 'quantity' => 1],
            ]
        ]);

        $this->productAvailabilityService->method('checkAvailability')->willReturn([
            'status' => true,
            'product' => 'product_name'
        ]);

        $orderMock = $this->createMock(\App\Entity\Order::class);
        $this->orderCreationService->method('createOrder')->willReturn($orderMock);

        $this->stockUpdateService->expects($this->once())->method('updateStock');
        $this->stockMovementService->expects($this->once())->method('recordStockMovement');

        $this->entityManager->expects($this->once())->method('beginTransaction');
        $this->entityManager->expects($this->once())->method('commit');
        $this->entityManager->expects($this->never())->method('rollback');  // Ensure rollback is not called on success

        $order = $this->clientOrderActionService->order($this->request);

        $this->assertSame($orderMock, $order);
    }

    public function testOrderFailsDueToStockAvailability(): void
    {
        $this->session->method('get')->willReturn(123);

        $this->request->method('getParsedBody')->willReturn([
            'items' => [
                ['id' => 1, 'quantity' => 2],
            ]
        ]);

        $this->productAvailabilityService->method('checkAvailability')->willReturn([
            'status' => false,
            'product' => 'product_name'
        ]);

        $this->entityManager->expects($this->once())->method('beginTransaction');
        $this->entityManager->expects($this->once())->method('rollback');
        $this->entityManager->expects($this->never())->method('commit');  // Ensure commit is not called

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product product_name is out of stock');
        $this->clientOrderActionService->order($this->request);
    }

    public function testOrderThrowsExceptionOnCreateOrderFailure(): void
    {
        $this->session->method('get')->willReturn(123);

        $this->request->method('getParsedBody')->willReturn([
            'items' => [
                ['id' => 1, 'quantity' => 2],
            ]
        ]);

        $this->productAvailabilityService->method('checkAvailability')->willReturn([
            'status' => true,
            'product' => 'product_name'
        ]);

        $this->orderCreationService->method('createOrder')->will($this->throwException(new Exception('Order creation failed')));

        $this->entityManager->expects($this->once())->method('beginTransaction');
        $this->entityManager->expects($this->once())->method('rollback');
        $this->entityManager->expects($this->never())->method('commit');  // Ensure commit is not called

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order creation failed');
        $this->clientOrderActionService->order($this->request);
    }
}
