<?php

declare(strict_types=1);

namespace App\Tests\Application\Api\V1;

use App\Entity\OrderStatus;
use App\Payment\LiqPay\Signature;
use App\Repository\OrderRepository;
use App\Tests\Concerns\WithFixtureLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class LiqPayPaymentCallbackTest extends WebTestCase
{
    use WithFixtureLoader;

    public function testLiqPayPaymentCallback(): void
    {
        $client = self::createClient();

        $orderRepository = $this->getContainer()->get(OrderRepository::class);

        $order = $orderRepository->findOneBy([
            'externalOrderId' => '111-liqpay',
        ]);

        // Just to be sure that the signature validation is happened.
        $signature = $this->createMock(Signature::class);
        $signature
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        // No any http related function should be invoked.
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->never())
            ->method('request');
        $httpClient
            ->expects($this->never())
            ->method('withOptions');

        self::getContainer()->set(Signature::class, $signature);
        self::getContainer()->set(HttpClientInterface::class, $httpClient);

        $successData = $this->loadFixture('liqpay/checkout-response-success.json');

        $content = http_build_query([
            'signature' => 'any-signature-because-its-validation-will-be-skipped',
            'data' => base64_encode($successData),
        ]);

        $client->request('POST', "/api/v1/payment/{$order->getUuid()}/handler", content: $content);

        $order = $orderRepository->findOneBy([
            'externalOrderId' => '111-liqpay',
        ]);

        // The order must be in payment_received status.
        $this->assertEquals(OrderStatus::PAYMENT_RECEIVED, $order->getStatus());
    }
}
