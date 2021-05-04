<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/page')]
class PageController extends AbstractController
{
    #[Route('/', name: 'page_index', methods: ['GET'])]
    public function index(PageRepository $pageRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('page/index.html.twig', [
            'pages' => $pageRepository->findByUser($this->getUser()),
        ]);
    }

    #[Route('/new', name: 'page_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $page->setUser($this->getUser());
            $entityManager->persist($page);
            $entityManager->flush();
            $this->refreshOrder();

            return $this->redirectToRoute('page_index');
        }

        return $this->render('page/new.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{user_id}/page/{z}', name: 'page_show', methods: ['GET'])]
    /**
     * @ParamConverter("page", options={"mapping": {"user_id": "user", "z": "z"}})
     */
    public function show(Page $page, Request $request): Response
    {
        if ($this->getUser()->getId() !== $page->getUser()->getId() && $this->getUser()->getRole() !== 'ROLE_ADMIN') {
            $this->addFlash(
                'danger',
                'Unauthorized'
            );
            return $this->redirectToRoute('page_index');
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('page/_list_show.html.twig', [
                'page' => $page,
            ]);
        }

        return $this->render('page/show.html.twig', [
            'page' => $page,
        ]);
    }

    #[Route('/{id}/edit', name: 'page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page, PageRepository $pageRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()->getId() !== $page->getUser()->getId()) {
            $this->addFlash(
                'danger',
                'Unauthorized'
            );
            return $this->redirectToRoute('page_index');
        }

        $currentZ          = $page->getZ();
        $currentUserId     = $page->getUser()->getId();
        $form              = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newZ = $form->getData()->getZ();
            if ($currentZ > $newZ) {
                $pageRepository->refreshOrderZ($currentUserId, $newZ, 'UP');
            } elseif ($currentZ < $newZ) {
                $pageRepository->refreshOrderZ($currentUserId, $newZ, 'DOWN');
            }
            
            $this->getDoctrine()->getManager()->flush();
            $this->refreshOrder();

            return $this->redirectToRoute('page_index');
        }

        return $this->render('page/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'page_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()->getId() !== $page->getUser()->getId()) {
            $this->addFlash(
                'danger',
                'Unauthorized'
            );
            return $this->redirectToRoute('page_index');
        }
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($page);
            $entityManager->flush();
        }

        $this->addFlash(
            'success',
            'Action réalisée avec succès'
        );

        return $this->redirectToRoute('page_index');
    }

    /**
     * Permet de réordonner proprement l'ensemble des pages en fonction de leur position z
     */
    private function refreshOrder()
    {
        $pages = $this->getDoctrine()->getRepository(Page::class)->findByUser($this->getUser());

        foreach ($pages as $order => $page) {
            $page->setZ($order + 1);
        }

        $this->getDoctrine()->getManager()->flush();
    }
}
