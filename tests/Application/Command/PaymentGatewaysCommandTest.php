<?php

declare(strict_types=1);

namespace Mirspay\Tests\Application\Command;

use Mirspay\Payment\Common\PaymentGatewayRegistry;
use Mirspay\Tests\Concerns\WithPaymentGateway;
use Symfony\Component\Console\Tester\CommandTester;

final class PaymentGatewaysCommandTest extends AbstractCommandTest
{
    use WithPaymentGateway;

    public function testCommandExecuteIsSuccessful(): void
    {
        $registry = new PaymentGatewayRegistry([]);

        self::getContainer()->set(PaymentGatewayRegistry::class, $registry);

        $command = $this->application->find('payment:gateways');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
    }

    public function testItListsPaymentGateways(): void
    {
        $registry = new PaymentGatewayRegistry([
            $this->makePaymentGatewayStub('a', 'AAA', false),
            $this->makePaymentGatewayStub('b', 'BBB', true),
        ]);

        self::getContainer()->set(PaymentGatewayRegistry::class, $registry);

        $command = $this->application->find('payment:gateways');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $display = $commandTester->getDisplay();
        $this->assertStringContainsString('AAA', $display);
        $this->assertStringContainsString('BBB', $display);
    }

    public function testItFiltersOnlyTestModePayments(): void
    {
        $registry = new PaymentGatewayRegistry([
            $this->makePaymentGatewayStub('a', 'AAA', false),
            $this->makePaymentGatewayStub('b', 'BBB', true),
        ]);

        self::getContainer()->set(PaymentGatewayRegistry::class, $registry);

        $command = $this->application->find('payment:gateways');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--test' => true,
        ]);

        $commandTester->assertCommandIsSuccessful();
        $display = $commandTester->getDisplay();
        $this->assertStringNotContainsString('AAA', $display);
        $this->assertStringContainsString('BBB', $display);
    }
}
