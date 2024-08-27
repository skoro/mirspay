<?php

declare(strict_types=1);

namespace App\Payment\Common;

use App\Payment\Common\Exception\PaymentGatewayIsNotRegisteredException;
use IteratorAggregate;

/**
 * Payment gateway registry.
 *
 * Contains all the payment gateways available for using in order payments.
 */
interface PaymentGatewayRegistryInterface extends IteratorAggregate
{
    /**
     * @return iterable<GatewayInterface>
     */
    public function getRegisteredGateways(): iterable;

    /**
     * @param non-empty-string $gatewayId
     * @throws PaymentGatewayIsNotRegisteredException The gateway is not registered.
     * @see GatewayInterface::getId()
     */
    public function getGatewayById(string $gatewayId): GatewayInterface;
}
