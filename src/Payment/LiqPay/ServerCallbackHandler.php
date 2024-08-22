<?php

declare(strict_types=1);

namespace App\Payment\LiqPay;

use App\Payment\Common\Message\RequestInterface;
use App\Payment\Common\Message\ServerCallbackHandlerInterface;
use App\Payment\LiqPay\Exception\InvalidResponseException;
use App\Payment\LiqPay\Exception\InvalidResponseSignatureException;
use App\Payment\LiqPay\Exception\ResponseDecodeException;
use App\Payment\LiqPay\Response\MessageDecoder;
use App\Payment\LiqPay\Response\PaymentStatusResponse;

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
