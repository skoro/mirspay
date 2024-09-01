<?php

declare(strict_types=1);

namespace App\Subscriber\Action;

use App\Entity\Order;
use App\Entity\Subscriber;
use App\Payment\Common\Message\ResponseInterface;
use App\Subscriber\Channel\NotificationChannelCollection;

class SendSubscriberNotificationAction
{
    public function __construct(
        private readonly NotificationChannelCollection $notificationChannelCollection,
    ) {
    }

    public function sendNotification(Subscriber $subscriber, Order $order, ResponseInterface $response): void
    {
        $channel = $this->notificationChannelCollection->getNotificationChannel($subscriber->getChannelType());
        $message = $this->notificationChannelCollection->getMessage($subscriber->getChannelMessage());

        $message->setOrder($order);
        $message->setResponse($response);

        $channel->send($message, $subscriber->getParams());
    }
}
