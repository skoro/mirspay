<?php

declare(strict_types=1);

namespace App\Payment\LiqPay\Request;

use App\Payment\Common\Message\PurchaseRequestInterface;
use App\Payment\Common\Message\RedirectResponseInterface;
use App\Payment\Common\Message\ResponseInterface;
use App\Payment\LiqPay\Exception\UnsupportedCurrencyException;
use App\Payment\LiqPay\Response\CheckoutResponse;
use InvalidArgumentException;
use LogicException;
use Money\Money;
use Override;
use Symfony\Contracts\HttpClient\ResponseInterface as HttpResponse;

/**
 * @link https://www.liqpay.ua/doc/api/internet_acquiring/checkout?tab=1
 */
final class CheckoutRequest extends AbstractRequest implements PurchaseRequestInterface
{
    public const string ACTION = 'pay';
    public const string CHECKOUT_URL = 'https://www.liqpay.ua/api/3/checkout';

    public function getAction(): string
    {
        return self::ACTION;
    }

    public function getRawData(): array
    {
        $money = $this->getMoney();

        return array_merge(
            parent::getRawData(),
            [
                // convert money to numeric
                'amount' => $money->getAmount() / 100,
                'currency' => $money->getCurrency()->getCode(),
            ]
        );
    }

    public function setOrderId(string $orderId): static
    {
        return $this->setParameter('order_id', $orderId);
    }

    public function getOrderId(): string
    {
        return (string) $this->getParameter('order_id');
    }

    public function setDescription(string $description): static
    {
        return $this->setParameter('description', $description);
    }

    public function getDescription(): string
    {
        return (string) $this->getParameter('description');
    }

    public function setMoney(Money $amount): static
    {
        return $this->setParameter('amount', $this->validateAmount($amount));
    }

    public function getMoney(): Money
    {
        return $this->getParameter('amount') ?? Money::{self::DEFAULT_CURRENCY}(0);
    }

    public function getRequestUrl(): string
    {
        return self::CHECKOUT_URL;
    }

    public function setReturnUrl(string $returnUrl): static
    {
        if (! $this->isValidUrl($returnUrl)) {
            throw new InvalidArgumentException("Return url '{$returnUrl}' is not a valid url.");
        }

        return $this->setParameter('result_url', $returnUrl);
    }

    public function getReturnUrl(): string
    {
        return (string) $this->getParameter('result_url');
    }

    public function setCallbackUrl(string $callbackUrl): static
    {
        if (! $this->isValidUrl($callbackUrl)) {
            throw new InvalidArgumentException("Callback url '{$callbackUrl}' is not a valid url.");
        }

        $this->setParameter('server_url', $callbackUrl);

        return $this;
    }

    public function getCallbackUrl(): string
    {
        return (string) $this->getParameter('server_url');
    }

    public function validate(): void
    {
        parent::validate();

        // @link https://www.liqpay.ua/doc/api/internet_acquiring/checkout?tab=1
        // "currency" will be extracted from "amount" further, @see self::getRawData()
        $this->validateParameters(
            'amount',
            'description',
            'order_id',
        );

        // By default, getMoney() return 0 if no amount already set,
        // we must be sure, we go with positive amount value into LiqPay api.
        $this->validateAmount($this->getMoney());
    }

    #[Override]
    public function send(): RedirectResponseInterface
    {
        $this->validate();

        $data = $this->messageEncoder->encode($this);
        $signature = $this->signature->make($data);

        $redirectUrl = sprintf(
    '%s?data=%s&signature=%s',
           self::CHECKOUT_URL,
            $data,
            $signature
        );

        return new CheckoutResponse($redirectUrl, $this);
    }

    protected function createResponse(HttpResponse $response): ResponseInterface
    {
        throw new LogicException('Checkout does not submit a request.');
    }

    /**
     * @throws UnsupportedCurrencyException The currency is not supported by the gateway.
     * @throws InvalidArgumentException Amount is zero or negative.
     */
    private function validateAmount(Money $amount): Money
    {
        $currency = $amount->getCurrency();
        if (! $this->isSupportedCurrency($currency)) {
            throw new UnsupportedCurrencyException($currency);
        }

        if (! $amount->isPositive()) {
            throw new InvalidArgumentException('Expected a positive amount value.');
        }

        return $amount;
    }
}
