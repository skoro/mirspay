<?php

declare(strict_types=1);

namespace Mirspay\Tests\Unit\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Mirspay\Entity\OrderStatus;
use Mirspay\Repository\SubscriberRepository;
use Mirspay\Subscriber\Action\AddHttpSubscriberAction;
use Mirspay\Subscriber\Channel\NotificationChannelCollection;
use Mirspay\Subscriber\Exception\SubscriberExistsException;
use Mirspay\Tests\Concerns\WithFaker;
use PHPUnit\Framework\TestCase;

final class AddHttpSubscriberActionTest extends TestCase
{
    use WithFaker;

    public function testUrlMustBeValid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid url');

        $action = new AddHttpSubscriberAction(
            $this->createStub(EntityManagerInterface::class),
            $this->createStub(SubscriberRepository::class),
            $this->createStub(NotificationChannelCollection::class),
        );

        $action->add(
            orderStatus: OrderStatus::PAYMENT_FAILED,
            url: '11111',
            channelMessage: 'test',
        );
    }

    public function testAddSubscriber(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist');
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $repository = $this->createMock(SubscriberRepository::class);
        $repository
            ->expects($this->once())
            ->method('hasSubscriber')
            ->willReturn(false);

        $channelCollection = $this->createMock(NotificationChannelCollection::class);
        $channelCollection
            ->expects($this->once())
            ->method('getNotificationChannelTypes')
            ->willReturn(['http']);
        $channelCollection
            ->expects($this->once())
            ->method('getMessageTypes')
            ->willReturn(['test']);

        $action = new AddHttpSubscriberAction($entityManager, $repository, $channelCollection);

        $action->add(OrderStatus::PAYMENT_RECEIVED, $this->faker()->url(), 'test');
    }

    public function testCannotAddSubscriberWithSameParameters(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->never())
            ->method('persist');
        $entityManager
            ->expects($this->never())
            ->method('flush');

        $repository = $this->createMock(SubscriberRepository::class);
        $repository
            ->expects($this->once())
            ->method('hasSubscriber')
            ->willReturn(true);

        $channelCollection = $this->createMock(NotificationChannelCollection::class);
        $channelCollection
            ->expects($this->once())
            ->method('getNotificationChannelTypes')
            ->willReturn(['http']);
        $channelCollection
            ->expects($this->once())
            ->method('getMessageTypes')
            ->willReturn(['test1']);

        $action = new AddHttpSubscriberAction($entityManager, $repository, $channelCollection);

        $this->expectException(SubscriberExistsException::class);
        $this->expectExceptionMessage('Subscriber with such parameters already exists');

        $action->add(OrderStatus::PAYMENT_RECEIVED, $this->faker()->url(), 'test1');
    }

    public function testHttpMethodMustBeAllowed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid http method "TEST", must be one of [POST,PUT,PATCH]');

        $action = new AddHttpSubscriberAction(
            $this->createStub(EntityManagerInterface::class),
            $this->createStub(SubscriberRepository::class),
            $this->createStub(NotificationChannelCollection::class),
        );

        $action->add(
            orderStatus: OrderStatus::PAYMENT_FAILED,
            url: $this->faker()->url(),
            channelMessage: 'foobar',
            httpMethod: 'TEST',
        );
    }
}
