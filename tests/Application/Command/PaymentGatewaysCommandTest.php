<?php

declare(strict_types=1);

namespace App\Tests\Application\Command;

use App\Payment\PaymentGatewayRegistry;
use App\Tests\Concerns\WithPaymentGateway;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class PaymentGatewaysCommandTest extends KernelTestCase
{
    use WithPaymentGateway;

    private Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->application = new Application(self::$kernel);
    }

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
