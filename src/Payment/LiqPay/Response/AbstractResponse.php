<?php

declare(strict_types=1);

namespace App\Payment\LiqPay\Response;

use App\Payment\Common\Message\AbstractResponse as CommonAbstractResponse;
use App\Payment\Common\Message\MessageInterface;
use App\Payment\Common\Message\NullRequest;
use App\Payment\Common\Message\RequestInterface;
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
