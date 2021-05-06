<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use App\Service\OrderService;
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
            'pages' => $this->getUser()->getPages()
        ]);
    }

    #[Route('/new', name: 'page_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PageRepository $pageRepository, OrderService $orderService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $page->setUser($this->getUser());
            $page->setZ($page->getZ() - 1); // -1 pour positionner au-dessus de l'élément existant d'un cran
            $entityManager->persist($page);
            $entityManager->flush();
            $orderService->refreshOrder($pageRepository->findBy(['user' => $this->getUser()], ['z' => 'ASC', 'created_at' => 'ASC']));

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
    public function edit(Request $request, Page $page, PageRepository $pageRepository, OrderService $orderService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()->getId() !== $page->getUser()->getId()) {
            $this->addFlash(
                'danger',
                'Unauthorized'
            );
            return $this->redirectToRoute('page_index');
        }

        $currentZ           = $page->getZ();
        $form               = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderService->handleOrderZ($page, $pageRepository->findBy(['user' => $this->getUser()], ['z' => 'ASC', 'created_at' => 'ASC']), $currentZ);
            $orderService->refreshOrder($pageRepository->findBy(['user' => $this->getUser()], ['z' => 'ASC', 'created_at' => 'ASC']));
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
}
