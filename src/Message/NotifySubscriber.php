<?php

declare(strict_types=1);

namespace App\Message;

use App\Payment\Common\Message\ResponseInterface;

final readonly class NotifySubscriber
{
    public function __construct(
        public int $orderId,
        public int $subscriberId,
        public ResponseInterface $response, // TODO: should be payment_processing id after transaction column will be added.
    ) {
    }
}
