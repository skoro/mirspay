<?php

declare(strict_types=1);

namespace App\Subscriber\Exception;

use Exception;

final class NotificationHandlerNotRegisteredException extends Exception
{
    public function __construct(
        public readonly string $notificationHandlerType,
    ) {
        parent::__construct("Subscriber notification handler type \"$this->notificationHandlerType\" is not registered.");
    }
}
