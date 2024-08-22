<?php

namespace App\Payment;

use App\Payment\Common\Exception\PaymentGatewayIsNotRegisteredException;
use App\Payment\Common\GatewayInterface;
use IteratorAggregate;

interface PaymentGatewayRegistryInterface extends IteratorAggregate
{
    /**
     * @return iterable<GatewayInterface>
     */
    public function getRegisteredGateways(): iterable;

    /**
     * @param non-empty-string $gatewayId
     * @throws PaymentGatewayIsNotRegisteredException The gateway is not registered.
     */
    public function getGatewayById(string $gatewayId): GatewayInterface;
}
