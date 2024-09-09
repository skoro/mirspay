<?php

declare(strict_types=1);

namespace Mirspay\Payment\Common\Exception;

class InvalidResponseException extends Exception
{
    public function __construct(string $message = "Invalid response from payment gateway", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}