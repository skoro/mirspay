<?php

declare(strict_types=1);

namespace Mirspay\Payment\Common\Builder;

use Mirspay\Entity\Order;
use Mirspay\Payment\Common\Message\PaymentStatusRequestInterface;

interface PaymentStatusRequestBuilderInterface
{
    public function build(Order $order): PaymentStatusRequestInterface;
}
