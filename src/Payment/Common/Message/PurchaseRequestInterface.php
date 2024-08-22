<?php

declare(strict_types=1);

namespace App\Payment\Common\Message;

use Money\Money;

/**
 * The payment gateway purchase request.
 */
interface PurchaseRequestInterface extends RequestInterface
{
    public function setOrderId(string $orderId): static;

    public function getOrderId(): string;

    public function setDescription(string $description): static;

    public function getDescription(): string;

    public function setMoney(Money $amount): static;

    public function getMoney(): Money;

    public function setReturnUrl(string $returnUrl): static;

    public function getReturnUrl(): string;

    public function setCallbackUrl(string $callbackUrl): static;

    public function getCallbackUrl(): string;

    /**
     * The purchase requests should return an url to a payment form.
     */
    public function send(): RedirectResponseInterface;
}
