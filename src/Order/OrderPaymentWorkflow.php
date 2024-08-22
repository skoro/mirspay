<?php

declare(strict_types=1);

namespace App\Order;

use App\Entity\Order;
use App\Entity\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;

class OrderPaymentWorkflow
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function setPaymentPending(Order $order): void
    {
        $order->setStatus(OrderStatus::PAYMENT_PENDING);
    }

    public function setPaymentReceived(): void
    {

    }
}