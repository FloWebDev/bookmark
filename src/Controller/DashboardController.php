<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(CacheInterface $cache): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        return $this->render('dashboard/index.html.twig', [
            'pages'            => $this->getUser()->getPages(),
            'gallery_template' => $cache->get('gallery-template', function() {
                return $this->renderView('_partials/_wallpapers_gallery.html.twig');
            })
        ]);
    }

    #[Route('/wallpaper-change', name: 'wallpaper_change', methods: ['POST'])]
    public function wallpaperChange(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $this->getUser()->setWallpaper($request->request->get('wallpaper'));
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'success'   => true
        ]);
    }
}
