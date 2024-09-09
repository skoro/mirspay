<?php

declare(strict_types=1);

namespace Mirspay\Tests\Unit\Payment\LiqPay\Request;

use Mirspay\Payment\Common\Message\MessageInterface;
use Mirspay\Payment\LiqPay\Request\MessageEncoder;
use Mirspay\Tests\Concerns\WithFaker;
use PHPUnit\Framework\TestCase;

final class MessageEncoderTest extends TestCase
{
    use WithFaker;

    public function testMessageEncodeAlgorithm(): void
    {
        $encoder = new MessageEncoder();

        $theData = [
            'id' => 123,
            'name' => $this->faker()->text(),
        ];

        $message = $this->createStub(MessageInterface::class);
        $message
            ->method('getRawData')
            ->willReturn($theData);

        $encoded = $encoder->encode($message);

        $expected = base64_encode(json_encode($theData));

        $this->assertEquals($expected, $encoded);
    }
}
