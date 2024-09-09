<?php

declare(strict_types=1);

namespace Mirspay\Payment\LiqPay\Request;

use Mirspay\Payment\Common\Message\AbstractRequest as CommonAbstractRequest;
use Mirspay\Payment\LiqPay\Signature;
use Money\Currency;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractRequest extends CommonAbstractRequest
{
    public const string API_URL = 'https://www.liqpay.ua/api';
    public const int VERSION = 3;
    public const array SUPPORTED_CURRENCIES = ['EUR', 'USD', 'UAH'];
    public const string DEFAULT_CURRENCY = 'UAH';

    /**
     * @param non-empty-string $publicKey The LiqPay api public key.
     */
    public function __construct(
        HttpClientInterface               $httpClient,
        protected readonly string         $publicKey,
        protected readonly Signature      $signature,
        protected readonly MessageEncoder $messageEncoder,
    ) {
        parent::__construct($httpClient);
    }

    public function initialize(): void
    {
        parent::initialize();

        $this->parameters->replace();

        // These parameters are always required in every request.
        $this->setParameter('version', $this->getVersion());
        $this->setParameter('public_key', $this->publicKey);
        $this->setParameter('action', $this->getAction());
    }

    /**
     * @return positive-int
     */
    public function getVersion(): int
    {
        return self::VERSION;
    }

    /**
     * @return non-empty-string The descendant must define the request action.
     */
    abstract public function getAction(): string;

    public function isSupportedCurrency(Currency $currency): bool
    {
        return in_array($currency->getCode(), self::SUPPORTED_CURRENCIES, true);
    }

    public function validate(): void
    {
        // These parameters must be presented in every request.
        // @see self::initialize()
        $this->validateParameters(
            'version',
            'public_key',
            'action',
        );
    }

    /**
     * @return array{body: array{data: string, signature: string}}
     */
    protected function getHttpRequestOptions(): array
    {
        $data = $this->messageEncoder->encode($this);

        return [
            'body' => [
                'data' => $data,
                'signature' => $this->signature->make($data),
            ],
        ];
    }
}
