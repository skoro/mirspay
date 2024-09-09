<?php

declare(strict_types=1);

namespace Mirspay\Tests\Unit\Payment\LiqPay\Response;

use Mirspay\Payment\Common\Message\MessageInterface;
use Mirspay\Payment\LiqPay\Response\PaymentStatusResponse;
use Mirspay\Tests\Concerns\WithFixtureLoader;
use PHPUnit\Framework\TestCase;

final class PaymentStatusResponseTest extends TestCase
{
    use WithFixtureLoader;

    public function testLiqPayPaymentStatusSuccessResponse(): void
    {
        $successData = $this->loadJsonFixture('liqpay/checkout-response-success.json');

        $message = $this->createStub(MessageInterface::class);
        $message
            ->method('getRawData')
            ->willReturn($successData);

        $response = PaymentStatusResponse::makeOfMessage($message);

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('', $response->getMessage());
        $this->assertEquals('123456789', $response->getTransactionId(), 'Payment transaction is not matched');
        $this->assertEquals(123456789, $response->getPaymentId(), 'Payment id is not matched');
    }
}
