<?php

declare(strict_types=1);

namespace App\Order\Workflow;

use App\Entity\Order;
use App\Payment\Common\Message\RequestInterface;
use App\Payment\Common\Message\ResponseInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderWorkflowFactory
{
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createFromContext(
        Order $order,
        RequestInterface | null $request = null,
        ResponseInterface | null $response = null,
    ): OrderWorkflowInterface {
        return new OrderWorkflow($this->em, $this->eventDispatcher, $order, $request, $response);
    }
}
