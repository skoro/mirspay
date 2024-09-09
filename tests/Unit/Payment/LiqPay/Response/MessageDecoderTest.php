<?php

declare(strict_types=1);

namespace Mirspay\Tests\Unit\Payment\LiqPay\Response;

use Mirspay\Payment\LiqPay\Exception\InvalidResponseException;
use Mirspay\Payment\LiqPay\Exception\ResponseDecodeException;
use Mirspay\Payment\LiqPay\Response\MessageDecoder;
use Mirspay\Payment\LiqPay\Response\SignedMessage;
use Mirspay\Tests\Concerns\WithFaker;
use PHPUnit\Framework\TestCase;

final class MessageDecoderTest extends TestCase
{
    use WithFaker;

    /**
     * @dataProvider postWithInvalidData
     */
    public function testArrayPostDataIsRequired($post): void
    {
        $decoder = new MessageDecoder();

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Response parameters not found');

        $decoder->decode($post);
    }

    public function testArrayPostDataMustBeBase64Encoded(): void
    {
        $decoder = new MessageDecoder();

        $this->expectException(ResponseDecodeException::class);
        $this->expectExceptionMessage('Cannot decode response');

        $decoder->decode(http_build_query([
            'data' => ' ',
            'signature' => $this->faker()->sha256(),
        ]));
    }

    /**
     * @dataProvider postWithInvalidSignature
     */
    public function testArrayPostSignatureIsRequired($post): void
    {
        $decoder = new MessageDecoder();

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Signature is missing in response data');

        $decoder->decode($post);
    }

    public function testDecodedDataMustBeJsonArray(): void
    {
        $data = base64_encode(json_encode('this is a string'));

        $decoder = new MessageDecoder();

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Expected data array but got "string"');

        $decoder->decode(http_build_query([
            'data' => $data,
            'signature' => $this->faker()->sha256(),
        ]));
    }

    public function testItReturnsSingedMessage(): void
    {
        $signature = $this->faker()->sha256();
        $data = ['payment_id' => 1234];

        $decoder = new MessageDecoder();
        $message = $decoder->decode(http_build_query([
            'data' => base64_encode(json_encode($data)),
            'signature' => $signature,
        ]));

        $this->assertInstanceOf(SignedMessage::class, $message);
        $this->assertEquals($signature, $message->getSignature());
        $this->assertEquals($data, $message->getRawData());
    }

    public function postWithInvalidData(): array
    {
        return [
            [
                '',
            ],
            [
                'data=&signature',
            ],
            [
                'data',
            ],
            [
                'data=',
            ],
        ];
    }

    public function postWithInvalidSignature()
    {
        return [
            [
                'data=123',
            ],
            [
                'data=1111&signature=',
            ],
        ];
    }
}
