<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Subscriber\Exception\ChannelMessageException;
use App\Subscriber\Exception\NotificationChannelException;

interface NotificationChannelInterface
{
    /**
     * @param array<string, mixed> $params Channel parameters.
     *
     * @throws NotificationChannelException
     * @throws ChannelMessageException
     */
    public function send(ChannelMessageInterface $message, array $params): void;
}
