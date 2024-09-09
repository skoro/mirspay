<?php

declare(strict_types=1);

namespace Mirspay\Subscriber\Exception;

use Exception;

final class SubscriberExistsException extends Exception
{
    protected $message = 'Subscriber with such parameters already exists';
}
