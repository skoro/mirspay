<?php

declare(strict_types=1);

namespace Mirspay\Tests\Unit\Payment\LiqPay\Request;

use InvalidArgumentException;
use Mirspay\Payment\Common\Exception\RequestParameterRequiredException;
use Mirspay\Payment\LiqPay\Exception\UnsupportedCurrencyException;
use Mirspay\Payment\LiqPay\Request\AbstractRequest;
use Mirspay\Payment\LiqPay\Request\CheckoutRequest;
use Mirspay\Payment\LiqPay\Request\MessageEncoder;
use Mirspay\Payment\LiqPay\Response\CheckoutResponse;
use Mirspay\Payment\LiqPay\Signature;
use Mirspay\Tests\Concerns\WithFaker;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CheckoutRequestTest extends TestCase
{
    use WithFaker;

    private function createCheckoutRequest(
        ?HttpClientInterface $httpClient = null,
        string $publicKey = 'public',
        ?Signature $signature = null,
        ?MessageEncoder $messageEncoder = null,
    ): CheckoutRequest {
        $httpClient ??= $this->createStub(HttpClientInterface::class);
        $signature ??= $this->createStub(Signature::class);
        $messageEncoder ??= $this->createStub(MessageEncoder::class);

        return new CheckoutRequest(
            httpClient: $httpClient,
            publicKey: $publicKey,
            signature: $signature,
            messageEncoder: $messageEncoder,
        );
    }

    public function testAction(): void
    {
        $checkout = $this->createCheckoutRequest();

        $this->assertEquals('pay', $checkout->getAction());
    }

    public function testCheckoutUrl(): void
    {
        $checkout = $this->createCheckoutRequest();

        $this->assertEquals('https://www.liqpay.ua/api/3/checkout', $checkout->getRequestUrl());
    }

    public function testCheckoutIsPOSTRequest(): void
    {
        $checkout = $this->createCheckoutRequest();

        $this->assertEquals('POST', $checkout->getHttpMethod());
    }

    public function testThrowsUnsupportedCurrency(): void
    {
        $checkout = $this->createCheckoutRequest();

        $this->expectException(UnsupportedCurrencyException::class);
        $this->expectExceptionMessage('Expected one of [EUR, USD, UAH] currency but got "ACA"');

        $checkout->setMoney(Money::ACA(44444));
    }

    public function testCheckoutAmountMustBePositive(): void
    {
        $checkout = $this->createCheckoutRequest();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a positive amount value');

        $checkout->setMoney(Money::USD(-99999));
    }

    public function testReturnsDefaultAmount0UAH(): void
    {
        $checkout = $this->createCheckoutRequest();

        $amount = $checkout->getMoney();

        $this->assertEquals('0', $amount->getAmount());
        $this->assertEquals('UAH', $amount->getCurrency()->getCode());
    }

    public function testCheckoutRequestIsInstanceOfAbstractRequest(): void
    {
        $checkout = $this->createCheckoutRequest();

        $this->assertInstanceOf(AbstractRequest::class, $checkout);
    }

    /**
     * @dataProvider amountDataProvider
     */
    public function testSetsAmountWithCurrency(Money $money, string $amount, string $currency): void
    {
        $checkout = $this->createCheckoutRequest();
        $checkout->setMoney($money);

        $this->assertEquals($amount, $checkout->getRawData()['amount']);
        $this->assertEquals($currency, $checkout->getRawData()['currency']);
    }

    public static function amountDataProvider(): array
    {
        return [
            [Money::USD(800), '8.00', 'USD'],
            [Money::EUR('99955'), '999.55', 'EUR'],
            [Money::UAH(123456789), '1234567.89', 'UAH'],
        ];
    }

    public function testValidateAmountIsRequired(): void
    {
        $checkout = $this->createCheckoutRequest();

        $checkout->initialize();

        $this->expectException(RequestParameterRequiredException::class);
        $this->expectExceptionMessage("The parameter 'amount' is required");

        $checkout->validate();
    }

    public function testValidateDescriptionIsRequired(): void
    {
        $checkout = $this->createCheckoutRequest();

        $checkout->initialize();

        $this->expectException(RequestParameterRequiredException::class);
        $this->expectExceptionMessage("The parameter 'description' is required");

        $checkout->setDescription('');
        $checkout->setMoney(Money::UAH(100_00));

        $checkout->validate();
    }

    public function testReturnUrlMustBeValid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Return url \'/this-is-not-url/\' is not a valid url');

        $checkout = $this->createCheckoutRequest();

        $checkout->setReturnUrl('/this-is-not-url/');

        $checkout->validate();
    }

    public function testCallbackUrlMustBeValidUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Callback url \'/this-is-not-url/\' is not a valid url');

        $checkout = $this->createCheckoutRequest();

        $checkout->setCallbackUrl('/this-is-not-url/');

        $checkout->validate();
    }

    public function testGetRawData(): void
    {
        $checkout = $this->createCheckoutRequest();
        $checkout->initialize();
        $checkout->setMoney(Money::USD(100_00));
        $checkout->setDescription($this->faker()->text());
        $checkout->setOrderId($this->faker()->uuid());
        $checkout->setCallbackUrl($this->faker()->url());
        $checkout->setReturnUrl($this->faker()->url());

        $data = $checkout->getRawData();

        $this->assertEquals([
            'version' => 3,
            'public_key' => 'public',
            'action' => 'pay',
            'currency' => 'USD',
            'amount' => 100,
            'description' => $checkout->getDescription(),
            'order_id' => $checkout->getOrderId(),
            'server_url' => $checkout->getCallbackUrl(),
            'result_url' => $checkout->getReturnUrl(),
        ], $data);
    }

    public function testCheckoutResponseInstance(): void
    {
        $checkout = $this->createCheckoutRequest();
        $checkout->initialize();
        $checkout->setOrderId($this->faker()->uuid());
        $checkout->setMoney(Money::USD(100_00));
        $checkout->setDescription($this->faker()->text());

        $response = $checkout->send();

        $this->assertInstanceOf(CheckoutResponse::class, $response);
    }

    public function testHttpClientIsActuallyNotUsedForCheckout(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->never())
            ->method('request');

        $checkout = $this->createCheckoutRequest($httpClient);

        $checkout->initialize();
        $checkout->setOrderId($this->faker()->uuid());
        $checkout->setMoney(Money::USD(100_00));
        $checkout->setDescription($this->faker()->text());

        $checkout->send();
    }

    public function testItReturnsCheckoutRedirectResponse(): void
    {
        $signature = $this->createMock(Signature::class);
        $signature
            ->expects($this->once())
            ->method('make')
            ->willReturn('1234567890');

        $messageEncoder = $this->createMock(MessageEncoder::class);
        $messageEncoder
            ->expects($this->once())
            ->method('encode')
            ->willReturn('abcdefgh');

        $checkout = $this->createCheckoutRequest(
            signature: $signature,
            messageEncoder: $messageEncoder,
        );

        $checkout->initialize();
        $checkout->setOrderId($this->faker()->uuid());
        $checkout->setMoney(Money::USD(100_00));
        $checkout->setDescription($this->faker()->text());

        $response = $checkout->send();

        $this->assertInstanceOf(CheckoutResponse::class, $response);
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('https://www.liqpay.ua/api/3/checkout?data=abcdefgh&signature=1234567890', $response->getRedirectUrl());
    }
}
