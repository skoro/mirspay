<?php

namespace App\Payment\Common\Builder;

use App\Entity\Order;
use App\Payment\Common\Message\PurchaseRequestInterface;

interface PurchaseRequestBuilderInterface
{
    public function build(Order $order): PurchaseRequestInterface;
}
