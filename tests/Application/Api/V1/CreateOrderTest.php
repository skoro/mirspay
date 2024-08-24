<?php

declare(strict_types=1);

namespace App\Tests\Application\Api\V1;

use App\Entity\Order;
use App\Payment\Common\Exception\PaymentGatewayIsNotRegisteredException;
use App\Payment\PaymentGatewayRegistry;
use App\Payment\PaymentGatewayRegistryInterface;
use App\Repository\OrderRepository;
use App\Tests\Concerns\WithOrderPostData;
use App\Tests\Concerns\WithPaymentGateway;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class CreateOrderTest extends WebTestCase
{
    use WithPaymentGateway;
    use WithOrderPostData;

    public function testExternalOrderIdAndPaymentGatewayMustBeUniqueOtherwise409CodeReturns(): void
    {
        $data = $this->makeOrderPostData([
            'order_num' => '1234567890',
            'payment_gateway' => 'foo',
        ]);

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository
            ->expects($this->once())
            ->method('findByExternalOrderIdAndPaymentGateway')
            ->with('1234567890', 'foo')
            ->willReturn(new Order());

        $client = self::createClient();

        self::getContainer()->set(OrderRepository::class, $orderRepository);

        $client->jsonRequest('POST' ,'/api/v1/order', $data);
        $resp = $client->getResponse();

        $this->assertEquals(409, $resp->getStatusCode());
    }

    public function testPaymentGatewayMustBeRegistered(): void
    {
        $data = $this->makeOrderPostData([
            'payment_gateway' => 'foo',
        ]);

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository
            ->expects($this->once())
            ->method('findByExternalOrderIdAndPaymentGateway')
            ->willReturn(null);

        $paymentGatewayRegistry = $this->createMock(PaymentGatewayRegistryInterface::class);
        $paymentGatewayRegistry
            ->expects($this->once())
            ->method('getGatewayById')
            ->with('foo')
            ->willThrowException(new PaymentGatewayIsNotRegisteredException('foo'));

        $client = self::createClient();

        self::getContainer()->set(PaymentGatewayRegistry::class, $paymentGatewayRegistry);
        self::getContainer()->set(OrderRepository::class, $orderRepository);

        $client->jsonRequest('POST' ,'/api/v1/order', $data);
        $resp = $client->getResponse();

        $this->assertEquals(400, $resp->getStatusCode());
    }

    public function testPersistOrderAndProducts(): void
    {
        $data = $this->makeOrderPostData();

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository
            ->expects($this->once())
            ->method('findByExternalOrderIdAndPaymentGateway')
            ->willReturn(null);

        $paymentGatewayRegistry = $this->createMock(PaymentGatewayRegistryInterface::class);
        $paymentGatewayRegistry
            ->expects($this->once())
            ->method('getGatewayById')
            ->willReturn($this->makePaymentGatewayStub('foo', 'foo'));

        $client = self::createClient();

        self::getContainer()->set(PaymentGatewayRegistry::class, $paymentGatewayRegistry);
        self::getContainer()->set(OrderRepository::class, $orderRepository);

        $client->jsonRequest('POST' ,'/api/v1/order', $data);
        $response = $client->getResponse();

        $json = json_decode($response->getContent(), associative: true);

        $this->assertTrue(Uuid::isValid($json['order']), 'Order uuid is not a valid uuid value.');
        $this->assertEquals(
            1,
            preg_match('/api\/v1\/order\/[0-9a-z-]+\/status/', $json['status_check']),
            '"status_check" value does not match to /api/v1/{uuid}/status'
        );
        $this->assertArrayHasKey('payment_redirect_url', $json);
        $this->assertEquals(201, $response->getStatusCode(), 'Response status code is not 201');
    }
}
