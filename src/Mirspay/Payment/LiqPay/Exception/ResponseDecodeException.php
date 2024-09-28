<?php

declare(strict_types=1);

namespace Mirspay\Payment\LiqPay\Exception;

class ResponseDecodeException extends InvalidResponseException
{
    public function __construct(string $message = "Cannot decode response data")
    {
        parent::__construct($message);
    }
}
