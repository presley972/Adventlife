<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user-super-admin',
    description: 'creation du Super Admin',
)]
class CreateUserSuperAdminCommand extends Command
{
    private $userPasswordHasher;
    private $entityManager;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;

        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'user email')
            ->addOption('mdp', null, InputOption::VALUE_NONE, 'password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User();
        $email = $input->getArgument('email');

        if ($email) {
            $user->setEmail($email);
            $io->note(sprintf('You passed an argument: %s', $email));
        }else{
            $user->setEmail('contact@adventlife.life');
            $io->note(sprintf('You passed an argument: %s', 'contact@Adventlife.life'));
        }

        if ($input->getOption('mdp')) {

            $mdp = $input->getOption('mdp');
        }else{
            $mdp = 'Adventlife2023';
        }
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $mdp
            )
        );

        $user->setIsVerified(true);
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('lutilisateur super admin '.$user->getEmail().' a été créer avec success');

        return Command::SUCCESS;
    }
}
