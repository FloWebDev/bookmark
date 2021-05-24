<?php

namespace App\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\FactoryInterface;
use App\Repository\PageRepository;
use Symfony\Component\Security\Core\Security;

class MenuBuilder
{
    private $factory;
    private $pageRepository;
    private $security;

    /**
     * Add any other dependency you need...
     */
    public function __construct(FactoryInterface $factory, PageRepository $pageRepository, Security $security)
    {
        $this->factory        = $factory;
        $this->pageRepository = $pageRepository;
        $this->security       = $security;
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $pages = $this->pageRepository->findBy([
            'user' => $this->security->getUser()], [
                'z' => 'ASC'
            ]);

        if ($pages) {
            foreach ($pages as $page) {
                $menu->addChild($page->getTitle(), ['route' => 'page_show', 'routeParameters' => [
                    'slug' => $page->getUser()->getSlug(),
                    'z'       => $page->getZ()
                ]]);
            }
        }

        return $menu;
    }
}
