<?php

declare(strict_types=1);

namespace Mirspay\Event;

use Mirspay\Entity\Order;

final class OrderWasCreated
{
    public function __construct(
        public readonly Order $order,
    ) {
    }
}
