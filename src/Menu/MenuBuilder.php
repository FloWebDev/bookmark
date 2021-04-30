<?php

namespace App\Menu;

use App\Repository\PageRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuBuilder
{
    private $factory;
    private $pageRepository;

    /**
     * Add any other dependency you need...
     */
    public function __construct(FactoryInterface $factory, PageRepository $pageRepository)
    {
        $this->factory        = $factory;
        $this->pageRepository = $pageRepository;
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $pages = $this->pageRepository->findAll();

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
