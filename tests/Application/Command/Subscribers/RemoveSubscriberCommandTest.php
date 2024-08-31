<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Subscribers;

use App\Entity\OrderStatus;
use App\Repository\SubscriberRepository;
use App\Subscriber\Action\AddHttpSubscriberAction;
use App\Tests\Application\Command\AbstractCommandTest;
use App\Tests\Concerns\WithFaker;
use Symfony\Component\Console\Tester\CommandTester;

final class RemoveSubscriberCommandTest extends AbstractCommandTest
{
    use WithFaker;

    public function testRemoveSubscriber(): void
    {
        /** @var AddHttpSubscriberAction $addHttpSubscriberAction */
        $addHttpSubscriberAction = $this->getContainer()->get(AddHttpSubscriberAction::class);

        $subscriber = $addHttpSubscriberAction->add(
            orderStatus: OrderStatus::PAYMENT_FAILED,
            url: $this->faker()->url(),
        );

        $command = $this->application->find('subscriber:remove');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'uuid' => $subscriber->getUuid(),
        ]);

        $commandTester->assertCommandIsSuccessful();

        /** @var SubscriberRepository $subscriberRepository */
        $subscriberRepository = $this->getContainer()->get(SubscriberRepository::class);

        $subscriber = $subscriberRepository->findOneBy(['uuid' => $subscriber->getUuid()]);

        $this->assertEmpty($subscriber);
    }
}
