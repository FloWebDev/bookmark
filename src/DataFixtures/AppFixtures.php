<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('test');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'test'));
        $user->setRole('ROLE_USER');
        $user->setEmail('test@test.io');
        $manager->persist($user);

        $manager->flush();
    }
}
