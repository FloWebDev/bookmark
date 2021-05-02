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

        $pages = $this->pageRepository->findByUser($this->security->getUser());

        if ($pages) {
            foreach ($pages as $page) {
                $menu->addChild($page->getTitle(), ['route' => 'page_show', 'routeParameters' => [
                    'id' => $page->getId()
                ]]);
            }
        }

        return $menu;
    }
}
