<?php

declare(strict_types=1);

namespace Mirspay\Order\Workflow;

use Mirspay\Entity\OrderStatus;

interface OrderWorkflowInterface
{
    public function setState(OrderStatus $orderStatus): void;
}
