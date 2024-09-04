<?php

declare(strict_types=1);

namespace App\Subscriber\Action;

use App\Entity\OrderStatus;
use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;
use App\Subscriber\Channel\NotificationChannelCollection;
use App\Subscriber\Exception\ChannelMessageNotRegistered;
use App\Subscriber\Exception\NotificationChannelNotRegisteredException;
use App\Subscriber\Exception\SubscriberExistsException;
use Doctrine\ORM\EntityManagerInterface;

class AddSubscriberAction
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SubscriberRepository $subscriberRepository,
        private readonly NotificationChannelCollection $notificationChannelCollection,
    ) {
    }

    /**
     * @throws SubscriberExistsException Trying to add a subscriber that already have the same order status,
     *                                   channel type and parameters.
     * @throws NotificationChannelNotRegisteredException The provided channel type is not registered.
     * @throws ChannelMessageNotRegistered The provided channel message is not registered.
     */
    public function addSubscriber(
        OrderStatus $orderStatus,
        string      $channelType,
        string      $channelMessage,
        array       $params,
    ): Subscriber {
        $this->validateChannelType($channelType);
        $this->validateChannelMessage($channelMessage);

        $subscriber = new Subscriber();
        $subscriber->setOrderStatus($orderStatus);
        $subscriber->setChannelType($channelType);
        $subscriber->setParams($params);
        $subscriber->setChannelMessage($channelMessage);
        $subscriber->setCreatedAtNow();
        $subscriber->generateHash();

        // Subscriber must have unique order status, notification type and parameters.
        $exists = $this->subscriberRepository->hasSubscriber($subscriber->getHash());
        if ($exists) {
            throw new SubscriberExistsException();
        }

        $this->em->persist($subscriber);
        $this->em->flush();

        return $subscriber;
    }

    /**
     * @throws NotificationChannelNotRegisteredException
     */
    protected function validateChannelType(string $channelType): void
    {
        if (! in_array($channelType, $this->notificationChannelCollection->getNotificationChannelTypes())) {
            throw new NotificationChannelNotRegisteredException($channelType);
        }
    }

    /**
     * @throws ChannelMessageNotRegistered
     */
    protected function validateChannelMessage(string $channelMessage): void
    {
        if (! in_array($channelMessage, $this->notificationChannelCollection->getMessageTypes())) {
            throw new ChannelMessageNotRegistered($channelMessage);
        }
    }
}
