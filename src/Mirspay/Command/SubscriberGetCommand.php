<?php

declare(strict_types=1);

namespace Mirspay\Command;

use Exception;
use Mirspay\Entity\OrderStatus;
use Mirspay\Repository\SubscriberRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'subscriber:get',
    description: 'Get subscriber or list/filter subscribers',
)]
final class SubscriberGetCommand extends Command
{
    public function __construct(
        private readonly SubscriberRepository $subscriberRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'uuid',
                InputArgument::OPTIONAL,
                'Get subscriber by its uuid',
            )
            ->addOption(
                'order-status',
                's',
                InputOption::VALUE_REQUIRED,
                'Filter by order status: ' . OrderStatus::formattedString(),
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $uuid = $input->getArgument('uuid');
            $orderStatus = $this->getOrderStatus($input->getOption('order-status'));

            if ($uuid) {
                $subscribers = $this->subscriberRepository->findBy(['uuid' => $uuid]);
            } else {
                $subscribers = $this->subscriberRepository->getList($orderStatus);
            }

            foreach ($subscribers as $subscriber) {
                $io->title($subscriber->getUuid()->toRfc4122());
                $io->block([
                    'Order status: ' . $subscriber->getOrderStatus()->value,
                    'Channel: ' . $subscriber->getChannelType(),
                    'Message: ' . $subscriber->getChannelMessage(),
                    'Added: ' . $subscriber->getCreatedAt()->format('Y-m-d H:i:s'),
                    'Parameters: ' . json_encode($subscriber->getParams(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                ]);
            }

            if (! $subscribers) {
                $io->warning('No subscribers found.');
            }
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }


        return Command::SUCCESS;
    }

    private function getOrderStatus(?string $value): OrderStatus | null
    {
        if (! $value) {
            return null;
        }

        $orderStatus = OrderStatus::tryFrom($value);
        if (!$orderStatus) {
            throw new Exception("Invalid order status '$value'. Allowed values: "
                . OrderStatus::formattedString());
        }

        return $orderStatus;
    }
}
