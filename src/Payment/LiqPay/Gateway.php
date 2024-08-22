<?php

declare(strict_types=1);

namespace App\Payment\LiqPay;

use App\Payment\Common\AbstractGateway;
use App\Payment\Common\Builder\CommonPaymentStatusRequestBuilder;
use App\Payment\Common\Builder\CommonPurchaseRequestBuilder;
use App\Payment\Common\Builder\PaymentStatusRequestBuilderInterface;
use App\Payment\Common\Builder\PurchaseRequestBuilderInterface;
use App\Payment\Common\Message\PaymentStatusRequestInterface;
use App\Payment\Common\Message\PurchaseRequestInterface;
use App\Payment\Common\Message\ServerCallbackHandlerInterface;
use App\Payment\LiqPay\Request\CheckoutRequest;
use App\Payment\LiqPay\Request\MessageEncoder;
use App\Payment\LiqPay\Request\PaymentStatusRequest;
use SensitiveParameter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Implementation of LiqPay payment gateway.
 *
 * @link https://www.liqpay.ua/doc
 */
final class Gateway extends AbstractGateway
{
    /**
     * @param non-empty-string $publicKey The LiqPay api public key.
     * @param non-empty-string $privateKey The LiqPay api private key.
     */
    public function __construct(
        HttpClientInterface                             $httpClient,
        private readonly string                         $publicKey,
        #[SensitiveParameter] private readonly string   $privateKey,
        private readonly Signature                      $signature,
        private readonly MessageEncoder                 $messageEncoder,
        private readonly ServerCallbackHandlerInterface $serverCallbackHandler,
    ) {
        parent::__construct($httpClient);
    }

    public function getId(): string
    {
        return 'liqpay';
    }

    public function getName(): string
    {
        return 'LiqPay';
    }

    public function isTestMode(): bool
    {
        return str_starts_with($this->publicKey, 'sandbox_')
            && str_starts_with($this->privateKey, 'sandbox_');
    }

    public function getPurchaseRequestBuilder(): PurchaseRequestBuilderInterface
    {
        return new CommonPurchaseRequestBuilder($this->createCheckoutRequest());
    }

    public function getPaymentStatusRequestBuilder(): PaymentStatusRequestBuilderInterface
    {
        return new CommonPaymentStatusRequestBuilder($this->createPaymentStatusRequest());
    }

    private function createCheckoutRequest(): PurchaseRequestInterface
    {
        return $this->createRequest(
            CheckoutRequest::class,
            $this->publicKey,
            $this->signature,
            $this->messageEncoder,
        );
    }

    private function createPaymentStatusRequest(): PaymentStatusRequestInterface
    {
        return $this->createRequest(
            PaymentStatusRequest::class,
            $this->publicKey,
            $this->signature,
            $this->messageEncoder,
        );
    }

    public function getServerCallbackHandler(): ServerCallbackHandlerInterface
    {
        return $this->serverCallbackHandler;
    }
}
