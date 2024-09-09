<?php

declare(strict_types=1);

namespace Mirspay\Payment\Common\Message;

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

    /**
     * Sets the frontend url to return user from the payment gateway form.
     *
     * It is not processing payment url, usually, it is something like "Thank you for your order" page.
     */
    public function setReturnUrl(string $returnUrl): static;

    public function getReturnUrl(): string;

    /**
     * Sets the server payment callback url.
     *
     * The callback url will be processed by the app in order to accept/deny the payment.
     *
     * @see ServerCallbackHandlerInterface::handleCallback()
     */
    public function setCallbackUrl(string $callbackUrl): static;

    public function getCallbackUrl(): string;

    /**
     * The purchase requests should return an url to a payment form.
     */
    public function send(): RedirectResponseInterface;
}
