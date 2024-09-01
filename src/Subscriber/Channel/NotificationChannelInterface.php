<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

interface NotificationChannelInterface
{
    /**
     * @param array<string, mixed> $params Channel parameters.
     */
    public function send(ChannelMessageInterface $message, array $params): void;
}
