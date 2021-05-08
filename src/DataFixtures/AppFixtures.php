<?php

namespace App\DataFixtures;

use App\Entity\Item;
use App\Entity\Page;
use App\Entity\User;
use App\Entity\Listing;
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
        // Création d'un administrateur
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'admin'));
        $admin->setRole('ROLE_ADMIN');
        $admin->setEmail('admin@admin.io');
        $manager->persist($admin);

        // POUR ADMIN
        // Création d'un utilisateur
        $user = new User();
        $user->setUsername('user');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'user'));
        $user->setRole('ROLE_USER');
        $user->setEmail('user@user.io');
        $manager->persist($user);

        // Création des pages
        $pages = [];
        for ($i = 0; $i < 6; $i++) {
            $page = new Page();
            $page->setTitle('Page n° ' . $i + 1);
            $page->setZ($i + 1);
            $page->setUser($admin);
            $manager->persist($page);
            $pages[] = $page;
        }

        // Création des listes
        $lists = [];
        for ($i = 0; $i < 25; $i++) {
            shuffle($pages);
            $list = new Listing();
            $list->setTitle('Liste n° ' . $i + 1);
            $list->setZ($i + 1);
            $list->setPage($pages[0]);
            $manager->persist($list);
            $lists[] = $list;
        }

        // Création des items
        for ($i = 0; $i < 500; $i++) {
            shuffle($lists);
            $item = new Item();
            $item->setTitle($i + 1 . ' - How can I do that I want to do thanks to Stack Overflow??!');
            $item->setUrl('https://www.google.com/');
            $item->setZ($i + 1);
            $item->setListing($lists[0]);
            $manager->persist($item);
        }

        // POUR USER
        // Création d'un utilisateur
        $pages = [];
        for ($i = 0; $i < 6; $i++) {
            $page = new Page();
            $page->setTitle('Page n° ' . $i + 1);
            $page->setZ($i + 1);
            $page->setUser($user);
            $manager->persist($page);
            $pages[] = $page;
        }

        // Création des listes
        $lists = [];
        for ($i = 0; $i < 25; $i++) {
            shuffle($pages);
            $list = new Listing();
            $list->setTitle('Liste n° ' . $i + 1);
            $list->setZ($i + 1);
            $list->setPage($pages[0]);
            $manager->persist($list);
            $lists[] = $list;
        }

        // Création des items
        for ($i = 0; $i < 500; $i++) {
            shuffle($lists);
            $item = new Item();
            $item->setTitle('Item n° ' . $i + 1);
            $item->setUrl('https://www.google.com/');
            $item->setZ($i + 1);
            $item->setListing($lists[0]);
            $manager->persist($item);
        }


        $manager->flush();
    }
}
