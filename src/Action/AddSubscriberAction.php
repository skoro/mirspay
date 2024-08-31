<?php

declare(strict_types=1);

namespace App\Action;

use App\Action\Exception\SubscriberExistsException;
use App\Entity\NotificationType;
use App\Entity\OrderStatus;
use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class AddSubscriberAction
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SubscriberRepository $subscriberRepository,
    ) {
    }

    /**
     * @throws SubscriberExistsException Trying to add a subscriber that already have the same order status,
     *                                   notification type and parameters.
     * @throws InvalidArgumentException Subscriber parameters cannot be empty.
     */
    public function addSubscriber(
        OrderStatus      $orderStatus,
        NotificationType $notificationType,
        array            $params,
    ): Subscriber {
        if (! $params) {
            throw new InvalidArgumentException('Subscriber params must not be empty.');
        }

        $subscriber = new Subscriber();
        $subscriber->setOrderStatus($orderStatus);
        $subscriber->setNotifyType($notificationType);
        $subscriber->setParams($params);
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
}
