<?php

namespace Mirspay\Payment\Common\Builder;

use Mirspay\Entity\Order;
use Mirspay\Payment\Common\Message\PurchaseRequestInterface;

interface PurchaseRequestBuilderInterface
{
    public function build(Order $order): PurchaseRequestInterface;
}
