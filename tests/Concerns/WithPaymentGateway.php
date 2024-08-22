<?php

declare(strict_types=1);

namespace App\Tests\Concerns;

use App\Payment\Common\GatewayInterface;
use PHPUnit\Framework\MockObject\Stub\Stub;

trait WithPaymentGateway
{
    private function makePaymentGatewayStub(string $id, string $name, bool $isTest = false): GatewayInterface|Stub
    {
        $gateway = $this->createStub(GatewayInterface::class);

        $gateway->method('getId')
            ->willReturn($id);
        $gateway->method('getName')
            ->willReturn($name);
        $gateway->method('isTestMode')
            ->willReturn($isTest);

        return $gateway;
    }
}