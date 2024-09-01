<?php

declare(strict_types=1);

namespace App\Command;

use App\Subscriber\Channel\NotificationChannelCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'subscriber:channels',
    description: 'Show a list of registered subscriber notification channels and messages',
)]
final class SubscriberChannelsCommand extends Command
{
    public function __construct(
        private readonly NotificationChannelCollection $channelCollection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $channelTypes = $this->channelCollection->getNotificationChannelTypes();
        $messageTypes = $this->channelCollection->getMessageTypes();

        if ($channelTypes) {
            $io->title('Notification channels:');
            $io->block($channelTypes);
        } else {
            $io->warning('No subscriber notification channels found.');
        }

        if ($messageTypes) {
            $io->title('Channel messages:');
            $io->block($messageTypes);
        } else {
            $io->warning('No channel messages found.');
        }

        return Command::SUCCESS;
    }
}
