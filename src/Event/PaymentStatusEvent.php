<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Order;
use App\Payment\Common\Message\ResponseInterface;
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
