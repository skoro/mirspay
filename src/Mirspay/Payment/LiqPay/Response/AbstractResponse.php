<?php

declare(strict_types=1);

namespace Mirspay\Payment\LiqPay\Response;

use Mirspay\Payment\Common\Message\AbstractResponse as CommonAbstractResponse;
use Mirspay\Payment\Common\Message\MessageInterface;
use Mirspay\Payment\Common\Message\NullRequest;
use Mirspay\Payment\Common\Message\RequestInterface;
use Override;

abstract class AbstractResponse extends CommonAbstractResponse
{
    public function __construct(
        protected readonly array $data,
        RequestInterface $request,
    ) {
        parent::__construct($request);
    }

    #[Override]
    public function getCode(): string
    {
        return (string) ($this->data['code'] ?? '');
    }

    public function getStatus(): string
    {
        return (string) ($this->data['status'] ?? '');
    }

    public static function makeOfMessage(MessageInterface $message, RequestInterface | null $request = null): static
    {
        return new static($message->getRawData(), $request ?? new NullRequest());
    }
}
