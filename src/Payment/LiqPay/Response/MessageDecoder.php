<?php

declare(strict_types=1);

namespace App\Payment\LiqPay\Response;

use App\Payment\LiqPay\Exception\InvalidResponseException;
use App\Payment\LiqPay\Exception\ResponseDecodeException;
use JsonException;

use const JSON_THROW_ON_ERROR;

class MessageDecoder
{
    /**
     * @param non-empty-string $body A form with parameters.
     * @throws JsonException
     * @throws ResponseDecodeException
     * @throws InvalidResponseException
     */
    public function decode(string $body): SignedMessage
    {
        /** @var array{data: string, signature: string} $post */
        parse_str($body, $post);

        $encodedData = $this->getEncodedData($post);
        $signature = $this->getSignature($post);

        if (! ($decoded = base64_decode($encodedData))) {
            throw new ResponseDecodeException();
        }

        $postData = json_decode($decoded, associative: true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($postData)) {
            throw new InvalidResponseException(sprintf('Expected data array but got "%s".', gettype($postData)));
        }

        return new SignedMessage(
            data: $postData,
            encodedData: $encodedData,
            signature: $signature,
        );
    }

    /**
     * @param array{data: string} $post
     * @return non-empty-string
     * @throws InvalidResponseException
     */
    private function getEncodedData(array $post): string
    {
        $data = (string) ($post['data'] ?? '');

        if (! $data) {
            throw new InvalidResponseException('Response parameters not found.');
        }

        return $data;
    }

    /**
     * @param array{signature: string} $post
     * @return non-empty-string
     * @throws InvalidResponseException
     */
    private function getSignature(array $post): string
    {
        $signature = (string) ($post['signature'] ?? '');

        if (! $signature) {
            throw new InvalidResponseException('Signature is missing in response data.');
        }

        return $signature;
    }
}
