<?php

declare(strict_types=1);

namespace Mirspay\Tests\Unit\Payment\LiqPay;

use Mirspay\Payment\LiqPay\Signature;
use Mirspay\Tests\Concerns\WithFaker;
use PHPUnit\Framework\TestCase;

final class SignatureTest extends TestCase
{
    use WithFaker;

    public function testMakeSignatureAlgorithm(): void
    {
        $privateKey = $this->faker()->sha256();
        $theData = $this->faker()->text();

        $signature = new Signature($privateKey);

        $result = $signature->make($theData);

        $expected = base64_encode(sha1($privateKey . $theData . $privateKey, binary: true));;

        $this->assertEquals($expected, $result);
    }
}
