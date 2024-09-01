<?php

declare(strict_types=1);

namespace App\Tests\Unit\Subscriber;

use App\Subscriber\Channel\ChannelMessageInterface;
use App\Subscriber\Channel\NotificationChannelCollection;
use App\Subscriber\Channel\NotificationChannelInterface;
use App\Subscriber\Exception\ChannelMessageNotRegistered;
use App\Subscriber\Exception\NotificationChannelNotRegisteredException;
use PHPUnit\Framework\TestCase;

final class NotificationChannelCollectionTest extends TestCase
{
    public function testAvailableChannelTypes(): void
    {
        $type1 = $this->createStub(NotificationChannelInterface::class);
        $type2 = $this->createStub(NotificationChannelInterface::class);

        $collection = new NotificationChannelCollection(
            channels: [
                'a' => $type1,
                'b' => $type2,
            ],
            messages: [],
        );

        $this->assertEquals(['a', 'b'], $collection->getNotificationChannelTypes());
    }

    public function testAvailableMessageTypes(): void
    {
        $type1 = $this->createStub(ChannelMessageInterface::class);
        $type2 = $this->createStub(ChannelMessageInterface::class);

        $collection = new NotificationChannelCollection(
            channels: [],
            messages: [
                'x' => $type1,
                'y' => $type2,
            ],
        );

        $this->assertEquals(['x', 'y'], $collection->getMessageTypes());
    }

    public function testThrowsChannelNotRegisteredException(): void
    {
        $collection = new NotificationChannelCollection(
            channels: [],
            messages: [],
        );

        $this->expectException(NotificationChannelNotRegisteredException::class);
        $this->expectExceptionMessage('Subscriber notification channel "aaa" is not registered');

        $collection->getNotificationChannel('aaa');
    }

    public function testThrowsMessageNotRegisteredException(): void
    {
        $collection = new NotificationChannelCollection(
            channels: [],
            messages: [],
        );

        $this->expectException(ChannelMessageNotRegistered::class);
        $this->expectExceptionMessage('Channel message type "msg" not registered');

        $collection->getMessage('msg');
    }
}