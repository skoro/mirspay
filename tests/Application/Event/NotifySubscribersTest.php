<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Entity\OrderStatus;
use App\Entity\PaymentProcessing;
use App\Event\OrderStatusWasChanged;
use App\EventListener\NotifySubscribersListener;
use App\Payment\Common\Message\ResponseInterface;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

final class NotifySubscribersTest extends KernelTestCase
{
    private EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = new EventDispatcher();
    }

    public function testEventOrderStatusWasChangedSendsMessage(): void
    {
        self::bootKernel();

        $orderRepository = self::getContainer()->get(OrderRepository::class);
        $listener = self::getContainer()->get(NotifySubscribersListener::class);

        $this->eventDispatcher->addListener(OrderStatusWasChanged::NAME, [$listener, 'onOrderStatusWasChanged']);

        // check fixtures for existing order.
        $order = $orderRepository->findOneBy([
            'externalOrderId' => '111-test',
        ]);
        // Because orders in fixtures are with "created" status,
        // set manually one of the status which subscribers expecting (see subscriber fixtures).
        $order->setStatus(OrderStatus::PAYMENT_RECEIVED);

        $response = $this->createStub(ResponseInterface::class);

        $event = new OrderStatusWasChanged(
            previousStatus: OrderStatus::CREATED,
            order: $order,
            paymentProcessing: new PaymentProcessing(),
            response: $response,
        );

        $this->eventDispatcher->dispatch($event, OrderStatusWasChanged::NAME);

        /** @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async');

        $this->assertCount(1, $transport->getSent());
    }
}
