<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

interface NotificationChannelInterface
{
    public function send(ChannelMessageInterface $message, array $params): void;
}
