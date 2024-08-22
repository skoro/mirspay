<?php

declare(strict_types=1);

namespace App\Tests\Unit\Payment\LiqPay;

use App\Payment\Common\Message\NullRequest;
use App\Payment\LiqPay\Exception\InvalidResponseException;
use App\Payment\LiqPay\Exception\InvalidResponseSignatureException;
use App\Payment\LiqPay\Request\CheckoutRequest;
use App\Payment\LiqPay\Request\MessageEncoder;
use App\Payment\LiqPay\Response\MessageDecoder;
use App\Payment\LiqPay\Response\PaymentStatusResponse;
use App\Payment\LiqPay\Response\SignedMessage;
use App\Payment\LiqPay\ServerCallbackHandler;
use App\Payment\LiqPay\Signature;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ServerCallbackHandlerTest extends TestCase
{
    /**
     * @dataProvider notStringCallbackDataProvider
     */
    public function testCallbackDataMustBeString(mixed $data): void
    {
        $handler = new ServerCallbackHandler(
            signature: $this->createMock(Signature::class),
            messageDecoder: $this->createStub(MessageDecoder::class),
        );

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Expected response data of string type ');

        $handler->handleCallback($data);
    }

    public function notStringCallbackDataProvider(): array
    {
        return [
            [false],
            [null],
            [123456],
            [[]],
            [new \stdClass],
        ];
    }

    public function testThrowsExceptionWhenSignatureIsInvalid(): void
    {
        $signature = $this->createMock(Signature::class);
        $signature
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $messageDecoder = $this->createMock(MessageDecoder::class);
        $messageDecoder
            ->expects($this->once())
            ->method('decode')
            ->willReturn(new SignedMessage([], '', ''));

        $handler = new ServerCallbackHandler(
            signature: $signature,
            messageDecoder: $messageDecoder,
        );

        $this->expectException(InvalidResponseSignatureException::class);
        $this->expectExceptionMessage('Response signature is invalid');

        $handler->handleCallback('it-must-be-base64-encoded-string');
    }

    public function testPaymentStatusResponseWithNullRequest(): void
    {
        $encodedData = base64_encode('1234567890');

        $signature = $this->createMock(Signature::class);
        $signature
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $messageDecoder = $this->createMock(MessageDecoder::class);
        $messageDecoder
            ->expects($this->once())
            ->method('decode')
            ->willReturn(new SignedMessage([], $encodedData, ''));

        $handler = new ServerCallbackHandler(
            signature: $signature,
            messageDecoder: $messageDecoder,
        );

        $response = $handler->handleCallback($encodedData, request: null);

        $this->assertInstanceOf(NullRequest::class, $response->getRequest());
        $this->assertInstanceOf(PaymentStatusResponse::class, $response);
    }

    public function testPaymentStatusResponseWithRequestObject(): void
    {
        $encodedData = base64_encode('1234567890');

        $signature = $this->createMock(Signature::class);
        $signature
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $messageDecoder = $this->createMock(MessageDecoder::class);
        $messageDecoder
            ->expects($this->once())
            ->method('decode')
            ->willReturn(new SignedMessage([], $encodedData, ''));

        $handler = new ServerCallbackHandler(
            signature: $signature,
            messageDecoder: $messageDecoder,
        );

        $request = new CheckoutRequest(
            httpClient: $this->createStub(HttpClientInterface::class),
            publicKey: 'does not matter',
            signature: $signature,
            messageEncoder: $this->createStub(MessageEncoder::class),
        );

        $response = $handler->handleCallback($encodedData, request: $request);

        $this->assertInstanceOf(CheckoutRequest::class, $response->getRequest());
    }
}
