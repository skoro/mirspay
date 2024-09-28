<?php

declare(strict_types=1);

namespace Mirspay\Payment\Common\Builder;

use Mirspay\Entity\Order;
use Mirspay\Payment\Common\Message\PaymentStatusRequestInterface;

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
