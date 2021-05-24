<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @link https://symfony.com/doc/current/console.html
 */
class CreateAdminCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create:admin';
    private $em;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();

        $this->em              = $em;
        $this->passwordEncoder = $encoder;
    }

    protected function configure()
    {
        $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Permet de créer le premier utilisateur administrateur.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Cette commande permet de créer le premier utilisateur avec un rôle administrateur.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Création de l'utilisateur admin
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'admin'));
        $admin->setRole('ROLE_ADMIN');
        $admin->setEmail('admin@admin.io');
        $admin->setSlug('aaa');
        $this->em->persist($admin);

        $this->em->flush();

        echo "Génération des données initiales terminées\n";

        return Command::SUCCESS;
    }
}
