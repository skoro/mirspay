<?php

declare(strict_types=1);

namespace App\Payment\Common\Builder;

use App\Entity\Order;
use App\Payment\Common\Message\PurchaseRequestInterface;

final readonly class CommonPurchaseRequestBuilder implements PurchaseRequestBuilderInterface
{
    public function __construct(
        private PurchaseRequestInterface $purchaseRequest,
    ) {
    }

    public function build(Order $order): PurchaseRequestInterface
    {
        $this->purchaseRequest->initialize();

        $this->purchaseRequest->setOrderId($order->getExternalOrderId());
        $this->purchaseRequest->setMoney($order->getAmount());
        $this->purchaseRequest->setDescription($order->getDescription());
        $this->purchaseRequest->setReturnUrl($order->getReturnUrl());

        return clone $this->purchaseRequest;
    }
}
