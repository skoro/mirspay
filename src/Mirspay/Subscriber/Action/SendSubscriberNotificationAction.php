<?php

declare(strict_types=1);

namespace Mirspay\Subscriber\Action;

use App\Subscriber\Exception;
use Mirspay\Entity\PaymentProcessing;
use Mirspay\Entity\Subscriber;
use Mirspay\Subscriber\Channel\NotificationChannelCollection;

class SendSubscriberNotificationAction
{
    public function __construct(
        private readonly NotificationChannelCollection $notificationChannelCollection,
    ) {
    }

    /**
     * @throws \Mirspay\Subscriber\Exception\NotificationChannelNotRegisteredException
     * @throws \Mirspay\Subscriber\Exception\NotificationChannelException
     * @throws \Mirspay\Subscriber\Exception\ChannelMessageNotRegistered
     * @throws \Mirspay\Subscriber\Exception\ChannelMessageException
     */
    public function sendNotification(Subscriber $subscriber, PaymentProcessing $paymentProcessing): void
    {
        $channel = $this->notificationChannelCollection->getNotificationChannel($subscriber->getChannelType());
        $message = $this->notificationChannelCollection->getMessage($subscriber->getChannelMessage());

        $message->setPaymentProcessing($paymentProcessing);

        $channel->send($message, $subscriber->getParams());
    }
}
