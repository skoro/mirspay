<?php

declare(strict_types=1);

namespace App\Tests\Unit\Subscriber;

use App\Entity\Order;
use App\Entity\Subscriber;
use App\Payment\Common\Message\ResponseInterface;
use App\Subscriber\Action\SendSubscriberNotificationAction;
use App\Subscriber\Channel\ChannelMessageInterface;
use App\Subscriber\Channel\NotificationChannelCollection;
use App\Subscriber\Channel\NotificationChannelInterface;
use PHPUnit\Framework\TestCase;

final class SendSubscriberNotificationActionTest extends TestCase
{
    public function testSendNotificationToSubscriber(): void
    {
        $params = [
            'a' => 1,
            'b' => 2,
        ];

        $subscriber = new Subscriber();
        $subscriber->setChannelType('in-test');
        $subscriber->setChannelMessage('test-message');
        $subscriber->setParams($params);

        $message = $this->createMock(ChannelMessageInterface::class);
        $message
            ->expects($this->once())
            ->method('setOrder');
        $message
            ->expects($this->once())
            ->method('setResponse');

        $channel = $this->createMock(NotificationChannelInterface::class);
        $channel
            ->expects($this->once())
            ->method('send')
            ->with($message, $params);

        $channelCollection = $this->createMock(NotificationChannelCollection::class);
        $channelCollection
            ->expects($this->once())
            ->method('getNotificationChannel')
            ->with('in-test')
            ->willReturn($channel);
        $channelCollection
            ->expects($this->once())
            ->method('getMessage')
            ->with('test-message')
            ->willReturn($message);

        $order = new Order();
        $response = $this->createStub(ResponseInterface::class);

        $action = new SendSubscriberNotificationAction($channelCollection);
        $action->sendNotification($subscriber, $order, $response);
    }
}
