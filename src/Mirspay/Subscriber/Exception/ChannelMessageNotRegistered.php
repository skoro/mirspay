<?php

declare(strict_types=1);

namespace Mirspay\Subscriber\Exception;

final class ChannelMessageNotRegistered extends ChannelMessageException
{
    public function __construct(
        public readonly string $channelMessageType,
    )
    {
        parent::__construct("Channel message type \"{$this->channelMessageType}\" not registered.");
    }
}
