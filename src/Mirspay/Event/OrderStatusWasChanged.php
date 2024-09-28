<?php

declare(strict_types=1);

namespace Mirspay\Event;

use Mirspay\Entity\Order;
use Mirspay\Entity\OrderStatus;
use Mirspay\Entity\PaymentProcessing;
use Mirspay\Payment\Common\Message\ResponseInterface;

final class OrderStatusWasChanged
{
    public function __construct(
        public readonly OrderStatus $previousStatus,
        public readonly Order $order,
        public readonly PaymentProcessing $paymentProcessing,
        public readonly ResponseInterface | null $response,
    ) {
    }
}
