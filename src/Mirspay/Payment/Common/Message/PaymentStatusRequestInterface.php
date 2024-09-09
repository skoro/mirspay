<?php

declare(strict_types=1);

namespace Mirspay\Payment\Common\Message;

/**
 * Payment status request.
 */
interface PaymentStatusRequestInterface extends RequestInterface
{
    /**
     * @param non-empty-string $orderId
     */
    public function setOrderId(string $orderId): static;

    /**
     * @return non-empty-string
     */
    public function getOrderId(): string;
}
