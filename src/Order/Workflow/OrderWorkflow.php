<?php

declare(strict_types=1);

namespace App\Order\Workflow;

use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Entity\PaymentProcessing;
use App\Payment\Common\Message\RequestInterface;
use App\Payment\Common\Message\ResponseInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderWorkflow
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Order $order,
        private readonly RequestInterface | null $request,
        private readonly ResponseInterface | null $response,
    ) {
    }

    public function setState(OrderStatus $orderStatus): void
    {
        $this->order->setStatus($orderStatus);
        $this->em->persist($this->order);

        $paymentProcessing = PaymentProcessing::create(
            order: $this->order,
            request: $this->request,
            response: $this->response,
        );
        $this->em->persist($paymentProcessing);

        $this->em->flush();
    }
}
