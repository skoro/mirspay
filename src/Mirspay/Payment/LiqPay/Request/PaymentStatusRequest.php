<?php

declare(strict_types=1);

namespace Mirspay\Payment\LiqPay\Request;

use Mirspay\Payment\Common\Message\PaymentStatusRequestInterface;
use Mirspay\Payment\LiqPay\Exception\InvalidResponseException;
use Mirspay\Payment\LiqPay\Response\PaymentStatusResponse;
use Mirspay\Payment\LiqPay\Signature;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as HttpResponse;

/**
 * @link https://www.liqpay.ua/doc/api/information/status_payment?tab=0
 */
final class PaymentStatusRequest extends AbstractRequest implements PaymentStatusRequestInterface
{
    public function __construct(
        HttpClientInterface $httpClient,
        string              $publicKey,
        Signature           $signature,
        MessageEncoder      $messageEncoder,
    ) {
        parent::__construct($httpClient, $publicKey, $signature, $messageEncoder);
    }

    public function getAction(): string
    {
        return 'status';
    }

    public function getRequestUrl(): string
    {
        return self::API_URL . '/request';
    }

    public function setOrderId(string $orderId): static
    {
        $this->parameters->set('order_id', $orderId);

        return $this;
    }

    public function getOrderId(): string
    {
        return (string) $this->parameters->get('order_id');
    }

    public function validate(): void
    {
        parent::validate();

        $this->validateParameters('order_id');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \JsonException
     * @throws InvalidResponseException
     */
    protected function createResponse(HttpResponse $response): PaymentStatusResponse
    {
        // The payment status response is not signed by the gateway,
        // it is just a json.
        $data = json_decode($response->getContent(), associative: true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($data)) {
            throw new InvalidResponseException(
                sprintf('Expected response array but got "%s".', gettype($data))
            );
        }

        return new PaymentStatusResponse($data, $this);
    }
}
