<?php

declare(strict_types=1);

namespace App\Subscriber\Action;

use App\Entity\PaymentProcessing;
use App\Entity\Subscriber;
use App\Subscriber\Channel\NotificationChannelCollection;
use App\Subscriber\Exception;

class SendSubscriberNotificationAction
{
    public function __construct(
        private readonly NotificationChannelCollection $notificationChannelCollection,
    ) {
    }

    /**
     * @throws Exception\NotificationChannelNotRegisteredException
     * @throws Exception\NotificationChannelException
     * @throws Exception\ChannelMessageNotRegistered
     * @throws Exception\ChannelMessageException
     */
    public function sendNotification(Subscriber $subscriber, PaymentProcessing $paymentProcessing): void
    {
        $channel = $this->notificationChannelCollection->getNotificationChannel($subscriber->getChannelType());
        $message = $this->notificationChannelCollection->getMessage($subscriber->getChannelMessage());

        $message->setPaymentProcessing($paymentProcessing);

        $channel->send($message, $subscriber->getParams());
    }
}
