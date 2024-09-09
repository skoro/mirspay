<?php

declare(strict_types=1);

namespace Mirspay\Payment\LiqPay\Response;

use Mirspay\Payment\Common\Message\RedirectResponseInterface;
use Mirspay\Payment\Common\Message\RequestInterface;
use Mirspay\Payment\LiqPay\Request\CheckoutRequest;

final readonly class CheckoutResponse implements RedirectResponseInterface
{
    public function __construct(
        private string $redirectUrl,
        private CheckoutRequest $request
    ) {
    }

    public function isRedirect(): true
    {
        return true;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getRawData(): string
    {
        return $this->redirectUrl;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function isSuccessful(): false
    {
        return false;
    }

    public function getMessage(): null
    {
        return null;
    }

    public function getCode(): null
    {
        return null;
    }

    public function getTransactionId(): string
    {
        return '';
    }

    public function jsonSerialize(): string
    {
        return $this->redirectUrl;
    }
}
