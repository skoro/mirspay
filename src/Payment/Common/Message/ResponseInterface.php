<?php

declare(strict_types=1);

namespace App\Payment\Common\Message;

interface ResponseInterface extends MessageInterface
{
    /**
     * Get the original request.
     */
    public function getRequest(): RequestInterface;

    /**
     * Does the response require redirect ?
     */
    public function isRedirect(): bool;

    public function isSuccessful(): bool;

    public function getMessage(): ?string;

    public function getCode(): ?string;

    public function getTransactionId(): string;
}
