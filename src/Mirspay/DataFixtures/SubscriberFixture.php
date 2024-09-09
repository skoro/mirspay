<?php

namespace Mirspay\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Mirspay\Entity\OrderStatus;
use Mirspay\Entity\Subscriber;
use Mirspay\Subscriber\Action\AddHttpSubscriberAction;

class SubscriberFixture extends Fixture
{
    public function __construct(
        private readonly AddHttpSubscriberAction $addHttpSubscriberAction,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->makeHttpSubscriber(OrderStatus::PAYMENT_RECEIVED));
        $manager->persist($this->makeHttpSubscriber(OrderStatus::PAYMENT_FAILED));
        $manager->persist($this->makeHttpSubscriber(OrderStatus::PAYMENT_PENDING));

        $manager->flush();
    }

    private function makeHttpSubscriber(
        OrderStatus $orderStatus,
        string $url = 'http://local-test.com',
        string $httpMethod = 'POST',
    ): Subscriber {
        return $this->addHttpSubscriberAction->add(
            orderStatus: $orderStatus,
            url: $url,
            channelMessage: 'simple',
            httpMethod: $httpMethod,
        );
    }
}
