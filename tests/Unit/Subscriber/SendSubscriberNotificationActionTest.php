<?php

declare(strict_types=1);

namespace Mirspay\Tests\Unit\Subscriber;

use Mirspay\Entity\Order;
use Mirspay\Entity\PaymentProcessing;
use Mirspay\Entity\Subscriber;
use Mirspay\Payment\Common\Message\ResponseInterface;
use Mirspay\Subscriber\Action\SendSubscriberNotificationAction;
use Mirspay\Subscriber\Channel\ChannelMessageInterface;
use Mirspay\Subscriber\Channel\NotificationChannelCollection;
use Mirspay\Subscriber\Channel\NotificationChannelInterface;
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
            ->method('setPaymentProcessing');

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
        $paymentProcessing = new PaymentProcessing();
        $response = $this->createStub(ResponseInterface::class);

        $action = new SendSubscriberNotificationAction($channelCollection);
        $action->sendNotification($subscriber, $paymentProcessing);
    }
}
