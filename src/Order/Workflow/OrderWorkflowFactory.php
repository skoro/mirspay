<?php

declare(strict_types=1);

namespace App\Order\Workflow;

use App\Entity\Order;
use App\Payment\Common\Message\RequestInterface;
use App\Payment\Common\Message\ResponseInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderWorkflowFactory
{
    public function __construct(
        protected readonly EntityManagerInterface $em,
    ) {
    }

    public function createFromContext(
        Order $order,
        RequestInterface | null $request = null,
        ResponseInterface | null $response = null,
    ): OrderWorkflow {
        return new OrderWorkflow($this->em, $order, $request, $response);
    }
}
