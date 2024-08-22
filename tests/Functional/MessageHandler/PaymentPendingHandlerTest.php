<?php

declare(strict_types=1);

namespace App\Tests\Functional\MessageHandler;

use App\Entity\OrderStatus;
use App\Entity\PaymentProcessing;
use App\Message\PurchaseOrder;
use App\MessageHandler\PaymentPendingHandler;
use App\Payment\Common\Message\RedirectResponseInterface;
use App\Payment\Common\Message\RequestInterface;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PaymentPendingHandlerTest extends KernelTestCase
{
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testOrderMustExist(): void
    {
        $orderId = random_int(1, 9999);

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository
            ->expects($this->once())
            ->method('find')
            ->with($orderId)
            ->willReturn(null);

        $handler = new PaymentPendingHandler($this->entityManager, $orderRepository);

        $this->expectException(EntityNotFoundException::class);

        $redirectResponse = $this->createStub(RedirectResponseInterface::class);
        $purchaseOrder = new PurchaseOrder($redirectResponse, $orderId);

        $handler($purchaseOrder);
    }

    public function testPaymentProcessingIsCreated(): void
    {
        $orderRepository = $this->getContainer()->get(OrderRepository::class);

        $order = $orderRepository->findOneBy([
            'externalOrderId' => '111-test',
        ]);

        $handler = new PaymentPendingHandler($this->entityManager, $orderRepository);

        $requestData = [
            'request' => 'data',
        ];

        $requestStub = $this->createMock(RequestInterface::class);
        $requestStub
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($requestData);

        $redirectResponse = $this->createMock(RedirectResponseInterface::class);
        $redirectResponse
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestStub);

        $message = new PurchaseOrder($redirectResponse, $order->getId());

        $handler($message);

        $paymentProcessingRepository = $this->entityManager->getRepository(PaymentProcessing::class);

        /** @var PaymentProcessing $paymentProcessing */
        $paymentProcessing = $paymentProcessingRepository->findOneBy([
            'order' => $order->getId(),
        ]);

        $this->assertEquals($message::class, $paymentProcessing->getMessage());
        $this->assertEquals($handler::class, $paymentProcessing->getHandler());
        $this->assertEquals($requestData, $paymentProcessing->getRequest());
    }

    public function testChangeOrderStatusToPaymentPending()
    {
        $orderRepository = $this->getContainer()->get(OrderRepository::class);

        $order = $orderRepository->findOneBy([
            'externalOrderId' => '111-test',
        ]);

        $handler = new PaymentPendingHandler($this->entityManager, $orderRepository);

        $redirectResponse = $this->createMock(RedirectResponseInterface::class);
        $redirectResponse
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->createStub(RequestInterface::class));

        $message = new PurchaseOrder($redirectResponse, $order->getId());

        $handler($message);

        $order = $orderRepository->find($order->getId());

        $this->assertEquals(OrderStatus::PAYMENT_PENDING, $order->getStatus());
    }
}
