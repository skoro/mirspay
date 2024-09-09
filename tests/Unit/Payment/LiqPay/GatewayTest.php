<?php

declare(strict_types=1);

namespace Mirspay\Tests\Unit\Payment\LiqPay;

use Mirspay\Payment\Common\Builder\CommonPurchaseRequestBuilder;
use Mirspay\Payment\Common\Message\ServerCallbackHandlerInterface;
use Mirspay\Payment\LiqPay\Gateway as LiqPayGateway;
use Mirspay\Payment\LiqPay\Request\MessageEncoder;
use Mirspay\Payment\LiqPay\Signature;
use Mirspay\Tests\Concerns\WithFaker;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GatewayTest extends TestCase
{
    use WithFaker;

    private HttpClientInterface $httpClientStub;
    private Signature $signature;
    private MessageEncoder $messageEncoder;
    private ServerCallbackHandlerInterface $serverCallbackHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClientStub = $this->createStub(HttpClientInterface::class);
        $this->signature = $this->createStub(Signature::class);
        $this->messageEncoder = $this->createStub(MessageEncoder::class);
        $this->serverCallbackHandler = $this->createStub(ServerCallbackHandlerInterface::class);
    }

    public function testGatewayName(): void
    {
        $gateway = new LiqPayGateway(
            $this->httpClientStub,
            $this->faker()->sha1(),
            $this->faker()->sha1(),
            $this->signature,
            $this->messageEncoder,
            $this->serverCallbackHandler,
        );

        $this->assertEquals('LiqPay', $gateway->getName());
    }

    public function testGatewayId(): void
    {
        $gateway = new LiqPayGateway(
            $this->httpClientStub,
            $this->faker()->sha1(),
            $this->faker()->sha1(),
            $this->signature,
            $this->messageEncoder,
            $this->serverCallbackHandler,
        );

        $this->assertEquals('liqpay', $gateway->getId());
    }

    public function testSandboxKeysGetTestModeTrue(): void
    {
        $gateway = new LiqPayGateway(
            $this->httpClientStub,
            'sandbox_' . $this->faker()->sha1(),
            'sandbox_' . $this->faker()->sha1(),
            $this->signature,
            $this->messageEncoder,
            $this->serverCallbackHandler,
        );

        $this->assertTrue($gateway->isTestMode());
    }

    public static function nonSandboxedKeys(): array
    {
        return [
            // public - private
            ['sandbox_1234567890', 'abcdef'],
            ['1232445', 'sandbox_abcd'],
        ];
    }

    /**
     * @dataProvider nonSandboxedKeys
     */
    public function testBothKeysMustHaveSandboxPrefix(string $public, string $private): void
    {
        $gateway = new LiqPayGateway(
            $this->httpClientStub,
            $public,
            $private,
            $this->signature,
            $this->messageEncoder,
            $this->serverCallbackHandler,
        );

        $this->assertFalse($gateway->isTestMode());
    }

    public function testLiqPayUsesDefaultPurchaseRequestBuilder(): void
    {
        $gateway = new LiqPayGateway(
            $this->httpClientStub,
            $this->faker()->sha1(),
            $this->faker()->sha1(),
            $this->signature,
            $this->messageEncoder,
            $this->serverCallbackHandler,
        );

        $purchaseBuilder = $gateway->getPurchaseRequestBuilder();

        $this->assertInstanceOf(CommonPurchaseRequestBuilder::class, $purchaseBuilder);
    }

    public function testServerCallbackHandlerIsSameAsInConstructor(): void
    {
        $gateway = new LiqPayGateway(
            $this->httpClientStub,
            $this->faker()->sha1(),
            $this->faker()->sha1(),
            $this->signature,
            $this->messageEncoder,
            $this->serverCallbackHandler,
        );

        $callbackHandler = $gateway->getServerCallbackHandler();

        $this->assertEquals($this->serverCallbackHandler, $callbackHandler);
    }
}
