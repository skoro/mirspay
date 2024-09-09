<?php

declare(strict_types=1);

namespace Mirspay\Event;

use Mirspay\Entity\Order;
use Mirspay\Payment\Common\Message\ResponseInterface;

/**
 * Occurs after the payment gateway callback handler was called.
 */
final class AfterPaymentCallbackHandlerEvent
{
    public function __construct(
        public Order $order,
        public ResponseInterface $response,
    ) {
    }
}
