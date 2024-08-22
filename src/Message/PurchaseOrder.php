<?php

declare(strict_types=1);

namespace App\Message;

use App\Payment\Common\Message\RedirectResponseInterface;

final readonly class PurchaseOrder
{
    public function __construct(
        public RedirectResponseInterface $redirectResponse,
        public int $orderId,
    ) {
    }
}
