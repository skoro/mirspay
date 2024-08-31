<?php

namespace App\DataFixtures;

use App\Action\AddHttpSubscriberAction;
use App\Entity\OrderStatus;
use App\Entity\Subscriber;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

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
            httpMethod: $httpMethod,
        );
    }
}
