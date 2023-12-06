<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:purge-registration',
    description: 'Purge the unverified users',
)]
class PurgeRegistrationCommand extends Command
{
    public function __construct(private readonly UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('days', InputArgument::OPTIONAL, 'The number of days since the users registration')
            ->addOption('delete', 'd', InputOption::VALUE_NONE, 'If set, the task will delete the users')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'If set, the task will not ask for confirmation')
        ;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int) ($input->getArgument('days') ?? 500_000);
        $delete = $input->getOption('delete');
        $force = $input->getOption('force');
        $table = new Table($output);
        $table->setHeaders(['Nom', 'Prénom', 'Email', 'Nombre de jours depuis l\'inscription']);

        $rows = [];
        foreach ($this->userRepository->findUnverifiedUsersSince($days) as $user) {
            $rows[] = [$user->getLastname(), $user->getFirstname(), $user->getEmail(), $user->getRegisteredAt()->diff(new \DateTime())->days];
        }

        $table->setRows($rows);
        $table->render();

        if ($delete) {
            if ($force || $io->confirm('Do you want to delete the users ?')) {
                $deleteCount = $this->userRepository->deleteUnverifiedUsersSince($days);
                $io->success("{$deleteCount} users have been deleted.");
            } else {
                $io->success('The users have not been deleted.');
            }
        }

        return Command::SUCCESS;
    }
}
