<?php

declare(strict_types=1);

namespace Mirspay\Payment\LiqPay\Exception;

class InvalidResponseSignatureException extends InvalidResponseException
{
    public function __construct(string $message = "Response signature is invalid.")
    {
        parent::__construct($message);
    }
}
