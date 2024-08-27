<?php

declare(strict_types=1);

namespace App\Tests\Application\Api\V1;

use App\Payment\Common\PaymentGatewayRegistry;
use App\Tests\Concerns\WithPaymentGateway;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PaymentControllerTest extends WebTestCase
{
    use WithPaymentGateway;

    public function testGetsAvailablePaymentGateways(): void
    {
        $client = self::createClient();

        $registry = new PaymentGatewayRegistry([
            $this->makePaymentGatewayStub('a', 'AAA', true),
        ]);
        self::getContainer()->set(PaymentGatewayRegistry::class, $registry);

        $client->request('GET', '/api/v1/payment/gateways');
        $resp = $client->getResponse();
        $json = $resp->getContent();

        $this->assertJson($json);
        $this->assertEquals([
            [
                'id' => 'a',
                'name' => 'AAA',
                'test_mode' => true,
            ],
        ], json_decode($json, associative: true));
        $this->assertResponseIsSuccessful();
    }
}
