<?php

declare(strict_types=1);

namespace App\Order\Workflow;

use App\Entity\OrderStatus;

interface OrderWorkflowInterface
{
    public function setState(OrderStatus $orderStatus): void;
}
