<?php

declare(strict_types=1);

namespace App\Payment\Common\Message;

abstract class AbstractResponse implements ResponseInterface
{
    public function __construct(
        private readonly RequestInterface $request,
    ) {
    }

    public function getRawData(): array
    {
        return [];
    }

    public function jsonSerialize(): array
    {
        return $this->getRawData();
    }

    public function getCode(): ?string
    {
        return null;
    }

    public function getTransactionId(): string
    {
        return '';
    }

    public function isSuccessful(): bool
    {
        return false;
    }

    public function getMessage(): ?string
    {
        return null;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
