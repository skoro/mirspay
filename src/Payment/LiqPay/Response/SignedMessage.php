<?php

declare(strict_types=1);

namespace App\Payment\LiqPay\Response;

use App\Payment\Common\Message\MessageInterface;

final readonly class SignedMessage implements MessageInterface
{
    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $encodedData Unmodified encoded response.
     * @param string $signature Signature can be optional, depends on request type.
     */
    public function __construct(
        private array $data,
        private string $encodedData,
        private string $signature,
    ) {
    }

    public function getRawData(): array
    {
        return $this->data;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getEncodedData(): string
    {
        return $this->encodedData;
    }

    /**
     * @return array{data: array, signature: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'data' => $this->data,
            'signature' => $this->signature,
        ];
    }
}