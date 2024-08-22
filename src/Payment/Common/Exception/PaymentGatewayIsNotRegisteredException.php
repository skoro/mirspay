<?php

declare(strict_types=1);

namespace App\Payment\Common\Exception;

final class PaymentGatewayIsNotRegisteredException extends Exception
{
    public function __construct(
        public readonly string $gatewayId,
    ) {
        parent::__construct("Payment gateway '{$this->gatewayId}' is not registered.");
    }
}
