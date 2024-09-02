<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Entity\OrderStatus;
use App\Entity\PaymentProcessing;
use App\Event\OrderStatusWasChanged;
use App\EventListener\NotifySubscribersListener;
use App\Payment\Common\Message\ResponseInterface;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

final class NotifySubscribersTest extends KernelTestCase
{
    private EventDispatcherInterface $eventDispatcher;
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->eventDispatcher = new EventDispatcher();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testEventOrderStatusWasChangedSendsMessage(): void
    {
        $orderRepository = self::getContainer()->get(OrderRepository::class);
        $listener = self::getContainer()->get(NotifySubscribersListener::class);

        $this->eventDispatcher->addListener('onOrderStatusWasChanged', [$listener, 'onOrderStatusWasChanged']);

        // check fixtures for existing order.
        $order = $orderRepository->findOneBy([
            'externalOrderId' => '111-test',
        ]);
        // Because orders in fixtures are with "created" status,
        // set manually one of the status which subscribers expecting (see subscriber fixtures).
        $order->setStatus(OrderStatus::PAYMENT_RECEIVED);

        $response = $this->createStub(ResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('jsonSerialize')->willReturn([
            'message' => 'ok',
        ]);

        $paymentProcessing = PaymentProcessing::create($order, null, $response);
        $this->entityManager->persist($paymentProcessing);
        $this->entityManager->flush();

        $event = new OrderStatusWasChanged(
            previousStatus: OrderStatus::CREATED,
            order: $order,
            paymentProcessing: $paymentProcessing,
            response: $response,
        );

        $this->eventDispatcher->dispatch($event, 'onOrderStatusWasChanged');

        /** @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async');

        $this->assertCount(1, $transport->getSent());
    }
}
