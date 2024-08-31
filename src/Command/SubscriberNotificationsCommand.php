<?php

namespace App\Command;

use App\Subscriber\Notification\NotificationHandlerCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'subscriber:notifications',
    description: 'Show a list of registered subscriber notification types',
)]
final class SubscriberNotificationsCommand extends Command
{
    public function __construct(
        private readonly NotificationHandlerCollection $handlerCollection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $handlerTypes = $this->handlerCollection->getHandlerTypes();

        if ($handlerTypes) {
            $io->title('Notification handlers:');
            $io->block($handlerTypes);
        } else {
            $io->warning('No subscriber notification handlers found.');
        }

        return Command::SUCCESS;
    }
}
