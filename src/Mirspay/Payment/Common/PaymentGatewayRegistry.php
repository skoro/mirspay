<?php

declare(strict_types=1);

namespace Mirspay\Payment\Common;

use ArrayIterator;
use Mirspay\Payment\Common\Exception\PaymentGatewayIsNotRegisteredException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Traversable;

/**
 * The implementation of Payment gateway registry.
 *
 * A payment gateway could be registered by adding `app.payment.gateway` tag in `services.yml` configuration:
 *
 *      services:
 *        App\Custom\PaymentGateway\MyGateway:
 *          tags: ['app.payment.gateway']
 */
final readonly class PaymentGatewayRegistry implements PaymentGatewayRegistryInterface
{
    /**
     * @param iterable<GatewayInterface> $paymentGateways
     */
    public function __construct(
        #[AutowireIterator('app.payment.gateway')] private iterable $paymentGateways,
    ) {
    }

    /**
     * @return iterable<GatewayInterface>
     */
    public function getRegisteredGateways(): iterable
    {
        return $this->paymentGateways;
    }

    /**
     * @param non-empty-string $gatewayId
     * @throws PaymentGatewayIsNotRegisteredException The gateway is not registered.
     */
    public function getGatewayById(string $gatewayId): GatewayInterface
    {
        foreach ($this->paymentGateways as $paymentGateway) {
            if ($paymentGateway->getId() === $gatewayId) {
                return $paymentGateway;
            }
        }

        throw new PaymentGatewayIsNotRegisteredException($gatewayId);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator(iterator_to_array($this->paymentGateways));
    }
}
