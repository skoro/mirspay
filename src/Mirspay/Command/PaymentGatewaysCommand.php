<?php

declare(strict_types=1);

namespace Mirspay\Command;

use Mirspay\Payment\Common\GatewayInterface;
use Mirspay\Payment\Common\PaymentGatewayRegistryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'payment:gateways',
    description: 'List of available payment gateways',
)]
final class PaymentGatewaysCommand extends Command
{
    public function __construct(
        private readonly PaymentGatewayRegistryInterface $paymentGateways,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('test', 't', InputOption::VALUE_NONE, 'Show only payment gateways in test mode.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $table->setHeaders(['Id', 'Name', 'In test']);

        $rows = [];
        $testFilter = $input->getOption('test');

        /** @var GatewayInterface $paymentGateway */
        foreach ($this->paymentGateways as $paymentGateway) {
            if ($testFilter && ! $paymentGateway->isTestMode()) {
                continue;
            }
            $rows[] = [
                $paymentGateway->getId(),
                $paymentGateway->getName(),
                $paymentGateway->isTestMode() ? '+' : '-',
            ];
        }

        $table->setStyle('borderless');
        $table->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }
}
