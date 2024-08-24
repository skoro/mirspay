<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Order;
use App\Payment\Common\Message\ResponseInterface;

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
