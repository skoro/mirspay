<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Subscribers;

use App\Entity\OrderStatus;
use App\Repository\SubscriberRepository;
use App\Tests\Application\Command\AbstractCommandTest;
use App\Tests\Concerns\WithFaker;
use Symfony\Component\Console\Tester\CommandTester;

final class RemoveCommandTest extends AbstractCommandTest
{
    use WithFaker;

    public function testRemoveSubscriber(): void
    {
        /** @var SubscriberRepository $subscriberRepository */
        $subscriberRepository = $this->getContainer()->get(SubscriberRepository::class);

        $subscriber = $subscriberRepository->findOneBy([
            'orderStatus' => OrderStatus::PAYMENT_PENDING,
        ]);

        $command = $this->application->find('subscriber:remove');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'uuid' => $subscriber->getUuid(),
        ]);

        $commandTester->assertCommandIsSuccessful();

        $subscriber = $subscriberRepository->findOneBy(['uuid' => $subscriber->getUuid()]);

        $this->assertEmpty($subscriber);
    }
}
