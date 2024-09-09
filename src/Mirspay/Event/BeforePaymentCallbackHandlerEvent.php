<?php

declare(strict_types=1);

namespace Mirspay\Event;

use Mirspay\Entity\Order;
use Mirspay\Payment\Common\GatewayInterface;

/**
 * Occurs before the payment gateway callback handler will be called.
 */
final class BeforePaymentCallbackHandlerEvent
{
    public function __construct(
        public mixed $content,
        public array $headers,
        public GatewayInterface $paymentGateway,
        public Order $order,
    ) {
    }
}
