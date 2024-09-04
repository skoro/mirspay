<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'subscriber:remove',
    description: 'Remove subscriber',
)]
final class SubscriberRemoveCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('uuid', InputArgument::REQUIRED, 'Subscriber uuid')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $uuid = $input->getArgument('uuid');

        /** @var SubscriberRepository $subscriberRepository */
        $subscriberRepository = $this->entityManager->getRepository(Subscriber::class);

        try {
            $subscriber = $subscriberRepository->findOneBy(['uuid' => $uuid]);
            if (! $subscriber) {
                throw new Exception("Subscriber \"$uuid\" not found.");
            }

            $this->entityManager->remove($subscriber);
            $this->entityManager->flush();

            $io->success("Subscriber \"$uuid\" has been removed.");
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
