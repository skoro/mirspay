<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Subscriber\Exception\ChannelMessageNotRegistered;
use App\Subscriber\Exception\NotificationChannelNotRegisteredException;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class NotificationChannelCollection
{
    /**
     * @var array<string, NotificationChannelInterface>
     */
    private readonly array $channels;

    private readonly array $messages;

    public function __construct(
        #[TaggedIterator('app.subscriber.channel', indexAttribute: 'type')]
        iterable $channels,

        #[TaggedIterator('app.subscriber.message', indexAttribute: 'type')]
        iterable $messages,
    ) {
        $this->channels = iterator_to_array($channels);
        $this->messages = iterator_to_array($messages);
    }

    /**
     * @param non-empty-string $type A notification channel type.
     * @throws NotificationChannelNotRegisteredException
     */
    public function getNotificationChannel(string $type): NotificationChannelInterface
    {
        return $this->channels[$type]
            ?? throw new NotificationChannelNotRegisteredException($type);
    }

    /**
     * @return string[]
     */
    public function getNotificationChannelTypes(): array
    {
        return array_keys($this->channels);
    }

    /**
     * @param non-empty-string $type A channel message type.
     * @throws ChannelMessageNotRegistered
     */
    public function getMessage(string $type): ChannelMessageInterface
    {
        return $this->messages[$type]
            ?? throw new ChannelMessageNotRegistered($type);
    }

    /**
     * @return string[]
     */
    public function getMessageTypes(): array
    {
        return array_keys($this->messages);
    }
}
