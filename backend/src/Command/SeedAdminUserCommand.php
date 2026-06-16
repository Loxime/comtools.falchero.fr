<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:seed-admin-user', description: 'Create or update the Maxime admin user.')]
final class SeedAdminUserCommand extends Command
{
    private const ADMIN_EMAIL = 'maximefalchero@gmail.com';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $users,
        private readonly string $adminPassword = 'ChangeMe123',
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = $this->users->findOneBy(['email' => self::ADMIN_EMAIL]);

        if ($user === null) {
            $user = (new User())
                ->setEmail(self::ADMIN_EMAIL)
                ->setPassword(password_hash($this->adminPassword, PASSWORD_BCRYPT))
                ->setUsername('Maxime')
                ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

            $this->entityManager->persist($user);
            $io->success('Admin user created.');
        } else {
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            $user->setIsActive(true);
            $io->success('Admin user already exists and has been updated.');
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
