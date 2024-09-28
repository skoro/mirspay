<?php

declare(strict_types=1);

namespace Mirspay\Order\Workflow;

use Doctrine\ORM\EntityManagerInterface;
use Mirspay\Entity\Order;
use Mirspay\Entity\OrderStatus;
use Mirspay\Entity\PaymentProcessing;
use Mirspay\Event\OrderStatusWasChanged;
use Mirspay\Payment\Common\Message\RequestInterface;
use Mirspay\Payment\Common\Message\ResponseInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class OrderWorkflow implements OrderWorkflowInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher,
        private Order $order,
        private RequestInterface | null $request,
        private ResponseInterface | null $response,
    ) {
    }

    public function setState(OrderStatus $orderStatus): void
    {
        $previousStatus = $this->order->getStatus();

        $this->order->setStatus($orderStatus);
        $this->em->persist($this->order);

        $paymentProcessing = PaymentProcessing::create(
            order: $this->order,
            request: $this->request,
            response: $this->response,
        );
        $this->em->persist($paymentProcessing);

        $this->em->flush();

        $this->dispatchOrderStatusEvent($previousStatus, $paymentProcessing);
    }

    private function dispatchOrderStatusEvent(OrderStatus $previousStatus, PaymentProcessing $paymentProcessing): void
    {
        $event = new OrderStatusWasChanged(
            previousStatus: $previousStatus,
            order: $this->order,
            paymentProcessing: $paymentProcessing,
            response: $this->response,
        );

        $this->eventDispatcher->dispatch($event);
    }
}
