<?php

declare(strict_types=1);

namespace App\Payment\Common\Builder;

use App\Entity\Order;
use App\Payment\Common\Message\PaymentStatusRequestInterface;

interface PaymentStatusRequestBuilderInterface
{
    public function build(Order $order): PaymentStatusRequestInterface;
}
