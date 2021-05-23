<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Constant\Constant;
use App\Util\OrderService;
use App\Repository\PageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
            'pageTitle' => Constant::PAGES_LIST_INDEX,
            'pages'     => $this->getUser()->getPages()
        ]);
    }

    #[Route('/new', name: 'page_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OrderService $orderService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $page->setUser($this->getUser());
            if ($form->get('position')->getData() === 'end') {
                $page->setZ(count($page->getUser()->getPages()) + 2);
            } else {
                $page->setZ(0);
            }

            $entityManager->persist($page);
            $entityManager->flush();
            $this->getDoctrine()->getManager()->refresh($page->getUser());
            $orderService->refreshOrder($page->getUser()->getPages());

            $this->addFlash(
                'success',
                Constant::SUCCESS_ACTION
            );

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('page/new.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{user_id}/order/{z}', name: 'page_show', methods: ['GET'])]
    /**
     * @ParamConverter("page", options={"mapping": {"user_id": "user", "z": "z"}})
     */
    public function show(Page $page, Request $request, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->getUser() || ($this->getUser()->getId() !== $page->getUser()->getId() && $this->getUser()->getRole() !== 'ROLE_ADMIN')) {
            $this->addFlash(
                'danger',
                Constant::FORBIDDEN
            );
            return $this->redirectToRoute('dashboard');
        }

        $session->set('page_id', $page->getId());

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => true,
                'msg'     => Constant::DASHBOARD_SENTENCE,
                'form'    => $this->renderView('_partials/_list_show.html.twig', [
                    'page' => $page
                ])
            ]);
        }

        return $this->render('page/show.html.twig', [
            'page' => $page,
        ]);
    }

    #[Route('/{id}/edit', name: 'page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()->getId() !== $page->getUser()->getId()) {
            $this->addFlash(
                'danger',
                Constant::FORBIDDEN
            );
            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                Constant::SUCCESS_ACTION
            );

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('page/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'page_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Page $page, OrderService $orderService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()->getId() !== $page->getUser()->getId()) {
            $this->addFlash(
                'danger',
                Constant::FORBIDDEN
            );
            return $this->redirectToRoute('dashboard');
        }

        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $user          = $page->getUser();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($page);
            $entityManager->flush();
            $entityManager->refresh($user);
            $orderService->refreshOrder($user->getPages());
        }

        $this->addFlash(
            'success',
            Constant::SUCCESS_ACTION
        );

        return $this->redirectToRoute('dashboard');
    }

    #[Route('/{id}/order/{direction}', name: 'page_order', methods: ['GET'])]
    public function order($direction, Page $page, OrderService $orderService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->getUser() || ($this->getUser()->getId() !== $page->getUser()->getId()
                    && $this->getUser()->getRole() !== 'ROLE_ADMIN')) {
            return $this->json([
                'success' => false,
                'msg'     => Constant::FORBIDDEN
            ], 403);
        }

        $orderService->handleUpAndDownPosition($page, $page->getUser()->getPages()->toArray(), $direction);
        $this->getDoctrine()->getManager()->refresh($page->getUser());
        $orderService->refreshOrder($page->getUser()->getPages());

        return $this->redirectToRoute('dashboard');
    }
}
