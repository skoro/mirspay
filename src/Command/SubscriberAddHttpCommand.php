<?php

declare(strict_types=1);

namespace App\Command;

use App\Action\AddHttpSubscriberAction;
use App\Entity\OrderStatus;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'subscriber:add-http',
    description: 'Add a order status notification subscriber of HTTP type.',
)]
final class SubscriberAddHttpCommand extends Command
{
    public function __construct(
        private readonly AddHttpSubscriberAction $addHttpSubscriberAction,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'Url to call.')
            ->addOption(
                'http-method',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Http method: ' . $this->getAllowedHttpMethods(),
                AddHttpSubscriberAction::DEFAULT_HTTP_METHOD,
            )
            ->addOption(
                'order-status',
                's',
                InputOption::VALUE_REQUIRED,
                'Expected order status: ' . OrderStatus::formattedString()
            )
        ;
    }

    private function getAllowedHttpMethods(): string
    {
        return implode(', ', AddHttpSubscriberAction::HTTP_METHODS);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $url = $input->getArgument('url');
        $httpMethod = $input->getOption('http-method');
        $orderStatusValue = $input->getOption('order-status');

        try {
            if (! ($orderStatus = OrderStatus::tryFrom($orderStatusValue))) {
                throw new Exception(
                    sprintf('Invalid order status: "%s". Expected: %s.',
                        $orderStatusValue,
                        OrderStatus::formattedString()
                    )
                );
            }

            $subscriber = $this->addHttpSubscriberAction->add($orderStatus, $url, $httpMethod);

            $io->success("Subscriber \"{$subscriber->getUuid()}\" has been added.");
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
