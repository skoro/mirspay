<?php

declare(strict_types=1);

namespace Mirspay\Subscriber\Exception;

final class NotificationChannelNotRegisteredException extends NotificationChannelException
{
    public function __construct(
        public readonly string $notificationChannelType,
    ) {
        parent::__construct("Subscriber notification channel \"$this->notificationChannelType\" is not registered.");
    }
}
