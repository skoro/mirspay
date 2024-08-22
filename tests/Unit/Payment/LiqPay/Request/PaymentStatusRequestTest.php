<?php

declare(strict_types=1);

namespace App\Tests\Unit\Payment\LiqPay\Request;

use App\Payment\Common\Exception\RequestParameterRequiredException;
use App\Payment\LiqPay\Request\MessageEncoder;
use App\Payment\LiqPay\Request\PaymentStatusRequest;
use App\Payment\LiqPay\Signature;
use App\Tests\Concerns\WithFaker;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PaymentStatusRequestTest extends TestCase
{
    use WithFaker;

    private function createPaymentRequest(
        ?HttpClientInterface $httpClient = null,
        ?string $publicKey = null,
        ?Signature $signature = null,
        ?MessageEncoder $messageEncoder = null,
    ): PaymentStatusRequest {
        return new PaymentStatusRequest(
            httpClient: $httpClient ?? $this->createStub(HttpClientInterface::class),
            publicKey: $publicKey ?? $this->faker()->sha1(),
            signature: $signature ?? $this->createStub(Signature::class),
            messageEncoder: $messageEncoder ?? $this->createStub(MessageEncoder::class),
        );
    }

    public function testPaymentStatusAction(): void
    {
        $request = $this->createPaymentRequest();

        $this->assertEquals('status', $request->getAction());
    }

    public function testPaymentStatusUrl(): void
    {
        $request = $this->createPaymentRequest();

        $this->assertEquals('https://www.liqpay.ua/api/request', $request->getRequestUrl());
    }

    public function testPaymentStatusIsPostRequest(): void
    {
        $request = $this->createPaymentRequest();

        $this->assertEquals('POST', $request->getHttpMethod());
    }

    public function testOrderIdIsRequired(): void
    {
        $request = $this->createPaymentRequest();

        $this->expectException(RequestParameterRequiredException::class);
        $this->expectExceptionMessage("The parameter 'order_id' is required");

        $request->initialize();
        $request->validate();
    }

    public function testPaymentStatusRequestParameters(): void
    {
        $request = $this->createPaymentRequest(publicKey: '123456');

        $request->initialize();
        $request->setOrderId('1111');

        $data = $request->getRawData();

        $this->assertEquals([
            'action' => 'status',
            'version' => 3,
            'public_key' => '123456',
            'order_id' => '1111',
        ], $data);
    }
}
