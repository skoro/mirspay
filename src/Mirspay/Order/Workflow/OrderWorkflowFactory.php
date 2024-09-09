<?php

declare(strict_types=1);

namespace Mirspay\Order\Workflow;

use Doctrine\ORM\EntityManagerInterface;
use Mirspay\Entity\Order;
use Mirspay\Payment\Common\Message\RequestInterface;
use Mirspay\Payment\Common\Message\ResponseInterface;
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
