<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Entity\PaymentProcessing;
use App\Payment\Common\Message\ResponseInterface;

final class OrderStatusWasChanged
{
    public const string NAME = 'order.status.changed';

    public function __construct(
        public readonly OrderStatus $previousStatus,
        public readonly Order $order,
        public readonly PaymentProcessing $paymentProcessing,
        public readonly ResponseInterface | null $response,
    ) {
    }
}
