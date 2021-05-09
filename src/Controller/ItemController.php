<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Constant\Constant;
use App\Service\OrderService;
use App\Repository\ItemRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/item')]
class ItemController extends AbstractController
{
    #[Route('/', name: 'item_index', methods: ['GET'])]
    public function index(ItemRepository $itemRepository): Response
    {
        return $this->render('item/index.html.twig', [
            'items' => $itemRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OrderService $orderService): Response
    {
        if ($request->isXmlHttpRequest()) {
            if (!$this->getUser()) {
                return $this->json([
                    'success'   => false,
                    'msg'       => Constant::FORBIDDEN
                ], 403);
            }

            $item = new Item();
            $form = $this->createForm(ItemType::class, $item, [
                'attr' => [
                    'id'     => 'createItemForm',
                    'action' => $this->generateUrl('item_new')
                ]
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $item->setZ(count($item->getListing()->getItems()) + 2);
                $entityManager->persist($item);
                $entityManager->flush();
                $orderService->refreshOrder($item->getListing()->getItems());

                return $this->json([
                    'success' => true
                ], 201);
            }

            return $this->json([
                'success'   => null,
                'formTitle' => Constant::ITEM_CREATE_FORM_TITLE,
                'form'      => $this->renderView('listing/_form.html.twig', [
                    'item'    => $item,
                    'form'    => $form->createView(),
                ])
            ]);
        }
    }

    #[Route('/{id}', name: 'item_show', methods: ['GET'])]
    public function show(Item $item): Response
    {
        return $this->render('item/show.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/{id}/edit', name: 'item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Item $item): Response
    {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('item_index');
        }

        return $this->render('item/edit.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'item_delete', methods: ['POST'])]
    public function delete(Request $request, Item $item): Response
    {
        if ($request->isXmlHttpRequest()) {
            if (!$this->getUser() || ($this->getUser()->getId() !== $item->getListing()->getPage()->getUser()->getId()
                    && $this->getUser()->getRole() !== 'ROLE_ADMIN')) {
                return $this->json([
                    'success' => false,
                    'msg'     => Constant::FORBIDDEN
                ], 403);
            }

            if ($this->isCsrfTokenValid('delete'.$item->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($item);
                $entityManager->flush();
            }

            return $this->json([
                'success'   => true
            ]);
        }
    }
}
