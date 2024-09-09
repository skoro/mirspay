<?php

declare(strict_types=1);

namespace Mirspay\Payment\LiqPay;

use Mirspay\Payment\Common\Message\RequestInterface;
use Mirspay\Payment\Common\Message\ServerCallbackHandlerInterface;
use Mirspay\Payment\LiqPay\Exception\InvalidResponseException;
use Mirspay\Payment\LiqPay\Exception\InvalidResponseSignatureException;
use Mirspay\Payment\LiqPay\Exception\ResponseDecodeException;
use Mirspay\Payment\LiqPay\Response\MessageDecoder;
use Mirspay\Payment\LiqPay\Response\PaymentStatusResponse;

/**
 * Checkout callback handler.
 */
final readonly class ServerCallbackHandler implements ServerCallbackHandlerInterface
{
    public function __construct(
        private Signature      $signature,
        private MessageDecoder $messageDecoder,
    ) {
    }

    /**
     *
     * @param mixed $data
     * @param array $params
     * @param RequestInterface|null $request
     * @return PaymentStatusResponse
     *
     * @throws InvalidResponseException A response data is not a string.
     * @throws InvalidResponseSignatureException A response signature is not valid.
     * @throws ResponseDecodeException Cannot decode a response data.
     * @throws \JsonException
     */
    public function handleCallback(
        mixed $data,
        array $params = [],
        RequestInterface | null $request = null,
    ): PaymentStatusResponse {
        if (! is_string($data)) {
            throw new InvalidResponseException(sprintf('Expected response data of string type but got "%s".', gettype($data)));
        }

        $signedMessage = $this->messageDecoder->decode($data);

        if (! $this->signature->isValid($signedMessage->getEncodedData(), $signedMessage->getSignature())) {
            throw new InvalidResponseSignatureException();
        }

        return PaymentStatusResponse::makeOfMessage($signedMessage, $request);
    }
}
