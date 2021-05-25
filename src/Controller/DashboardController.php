<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('dashboard/index.html.twig', [
            'pages'     => $this->getUser()->getPages()
        ]);
    }

    #[Route('/wallpaper-change', name: 'wallpaper_change', methods: ['POST'])]
    public function wallpaperChange(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->getUser()->setWallpaper($request->request->get('wallpaper'));
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'success'   => true
        ]);
    }
}
