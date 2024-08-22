<?php

declare(strict_types=1);

namespace App\Payment\Common\Builder;

use App\Entity\Order;
use App\Payment\Common\Message\PaymentStatusRequestInterface;

final readonly class CommonPaymentStatusRequestBuilder implements PaymentStatusRequestBuilderInterface
{
    public function __construct(
        private PaymentStatusRequestInterface $request,
    ) {
    }

    public function build(Order $order): PaymentStatusRequestInterface
    {
        $this->request->initialize();

        $this->request->setOrderId($order->getExternalOrderId());

        return clone $this->request;
    }
}
