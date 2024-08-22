<?php

declare(strict_types=1);

namespace App\Message;

final readonly class ProcessPaymentCallback
{
    public function __construct(
        public int $orderId,
        public string $content,
        public array $headers = [],
    ) {
    }
}
