<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Subscribers;

use App\Entity\OrderStatus;
use App\Repository\SubscriberRepository;
use App\Subscriber\Channel\NotificationChannelCollection;
use App\Tests\Application\Command\AbstractCommandTest;
use App\Tests\Concerns\WithFaker;
use Symfony\Component\Console\Tester\CommandTester;

final class AddHttpCommandTest extends AbstractCommandTest
{
    use WithFaker;

    public function testAddHttpSubscriber(): void
    {
        $url = $this->faker()->url();
        $httpMethod = 'PUT';
        $orderStatus = OrderStatus::PAYMENT_PENDING;

        $channelCollection = $this->createMock(NotificationChannelCollection::class);
        $channelCollection
            ->expects($this->once())
            ->method('getNotificationChannelTypes')
            ->willReturn(['http']);
        $channelCollection
            ->expects($this->once())
            ->method('getMessageTypes')
            ->willReturn(['test']);

        $this->getContainer()->set(NotificationChannelCollection::class, $channelCollection);

        $command = $this->application->find('subscriber:add-http');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'url' => $url,
            '--channel-message' => 'test',
            '--order-status' => $orderStatus->value,
            '--http-method' => $httpMethod,
        ]);

        $commandTester->assertCommandIsSuccessful($commandTester->getDisplay());

        $display = $commandTester->getDisplay();

        $this->assertEquals(
            1,
            preg_match('/Subscriber "(.+)" has been added/', $display, $matches),
            message: 'Cannot get subscriber uuid from command output.'
        );

        $uuid = $matches[1];

        $subscriberRepository = $this->getContainer()->get(SubscriberRepository::class);
        $subscriber = $subscriberRepository->findOneBy(['uuid' => $uuid]);

        $this->assertNotEmpty($subscriber, 'Subscriber not found');

        $this->assertEquals($orderStatus, $subscriber->getOrderStatus());

        $this->assertEquals([
            'url' => $url,
            'method' => $httpMethod,
        ], $subscriber->getParams());
    }
}
