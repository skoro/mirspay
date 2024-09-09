<?php

declare(strict_types=1);

namespace Mirspay\Tests\Application\Command\Subscribers;

use Mirspay\Subscriber\Channel\NotificationChannelCollection;
use Mirspay\Tests\Application\Command\AbstractCommandTest;
use Symfony\Component\Console\Tester\CommandTester;

final class ChannelsCommandTest extends AbstractCommandTest
{
    public function testListChannelsAndMessages(): void
    {
        $channelCollection = $this->createMock(NotificationChannelCollection::class);
        $channelCollection
            ->expects($this->once())
            ->method('getNotificationChannelTypes')
            ->willReturn(['test-channel']);
        $channelCollection
            ->expects($this->once())
            ->method('getMessageTypes')
            ->willReturn(['test-message']);

        $this->getContainer()->set(NotificationChannelCollection::class, $channelCollection);

        $command = $this->application->find('subscriber:channels');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $display = $commandTester->getDisplay();

        $this->assertStringContainsString('test-channel', $display, 'Channel type is missing in command output.');
        $this->assertStringContainsString('test-message', $display, 'Message type is missing in command output.');
    }
}
