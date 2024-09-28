<?php

declare(strict_types=1);

namespace Mirspay\Event;

use Mirspay\Entity\Order;
use Mirspay\Payment\Common\Message\ResponseInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The event occurs when payment status was requested.
 */
final class PaymentStatusEvent extends Event
{
    public function __construct(
        public Order $order,
        public ResponseInterface $paymentStatusResponse,
    ) {
    }
}
