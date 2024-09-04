<?php

declare(strict_types=1);

namespace App\Message;

final readonly class NotifySubscriber
{
    public function __construct(
        public int     $orderId,
        public int     $subscriberId,
        public int     $paymentProcessingId,
        public string  $transactionId,
        public ?string $responseMessage,
    ) {
    }
}
