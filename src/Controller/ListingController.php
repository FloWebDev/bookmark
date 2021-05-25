<?php

namespace App\Controller;

use App\Entity\Listing;
use App\Form\ListingType;
use App\Constant\Constant;
use App\Util\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/listing')]
class ListingController extends AbstractController
{
    #[Route('/new', name: 'listing_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OrderService $orderService): Response
    {
        if ($request->isXmlHttpRequest()) {
            if (!$this->getUser()) {
                return $this->json([
                    'success'   => false,
                    'msg'       => Constant::FORBIDDEN
                ], 403);
            }
            $listing = new Listing();
            $form    = $this->createForm(ListingType::class, $listing, [
                'attr' => [
                    'id'     => 'createListForm',
                    'action' => $this->generateUrl('listing_new')
                ]
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (!$this->getUser() || $listing->getPage()->getUser()->getId() !== $this->getUser()->getId()) {
                    return $this->json([
                        'success'   => false,
                        'msg'       => Constant::FORBIDDEN
                    ], 403);
                }

                $entityManager = $this->getDoctrine()->getManager();
                if ($form->get('position')->getData() === 'end') {
                    $listing->setZ(count($listing->getPage()->getListings()) + 2);
                } else {
                    $listing->setZ(0);
                }

                $entityManager->persist($listing);
                $entityManager->flush();
                $this->getDoctrine()->getManager()->refresh($listing->getPage());
                $orderService->refreshOrder($listing->getPage()->getListings());

                return $this->json([
                    'success' => true
                ], 201);
            }

            return $this->json([
                'success'   => null,
                'formTitle' => Constant::LIST_CREATE_FORM_TITLE,
                'form'      => $this->renderView('listing/_form.html.twig', [
                    'listing' => $listing,
                    'form'    => $form->createView(),
                ])
            ]);
        }
    }

    #[Route('/{id}/edit', name: 'listing_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Listing $listing): Response
    {
        if ($request->isXmlHttpRequest()) {
            if ($this->getUser()->getId() !== $listing->getPage()->getUser()->getId()) {
                return $this->json([
                    'success'   => false,
                    'msg'       => Constant::FORBIDDEN
                ], 403);
            }

            $form               = $this->createForm(ListingType::class, $listing, [
                'attr' => [
                    'id'     => 'editListForm',
                    'action' => $this->generateUrl('listing_edit', [
                        'id' => $listing->getId()
                    ])
                ]
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (!$this->getUser() || $listing->getPage()->getUser()->getId() !== $this->getUser()->getId()) {
                    return $this->json([
                        'success'   => false,
                        'msg'       => Constant::FORBIDDEN
                    ], 403);
                }

                $this->getDoctrine()->getManager()->flush();

                return $this->json([
                    'success' => true
                ]);
            }

            return $this->json([
                'success'   => null,
                'formTitle' => Constant::LIST_UPDATE_FORM_TITLE . $listing->getTitle(),
                'form'      => $this->renderView('listing/_form.html.twig', [
                    'listing' => $listing,
                    'form'    => $form->createView(),
                ])
            ]);
        }
    }

    #[Route('/{id}/delete', name: 'listing_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Listing $listing, OrderService $orderService): Response
    {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('GET')) {
                if (!$this->getUser() || $this->getUser()->getId() !== $listing->getPage()->getUser()->getId()) {
                    return $this->json([
                        'success' => false,
                        'msg'     => Constant::FORBIDDEN
                    ], 403);
                }

                return $this->json([
                    'success'   => null,
                    'title'     => Constant::LIST_DELETE_MODAL_TITLE . $listing->getTitle(),
                    'alert'     => Constant::LIST_DELETE_MODAL_ALERT,
                    'form'      => $this->renderView('listing/_delete_form.html.twig', [
                        'listing' => $listing
                    ])
                ]);
            } elseif ($request->isMethod('POST')) {
                if (!$this->getUser() || $this->getUser()->getId() !== $listing->getPage()->getUser()->getId()) {
                    return $this->json([
                        'success' => false,
                        'msg'     => Constant::FORBIDDEN
                    ], 403);
                }

                if ($this->isCsrfTokenValid('delete'.$listing->getId(), $request->request->get('_token'))) {
                    $currentPage   = $listing->getPage();
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($listing);
                    $entityManager->flush();
                    $entityManager->refresh($currentPage);
                    $orderService->refreshOrder($currentPage->getListings());
                }

                return $this->json([
                    'success'   => true
                ]);
            }
        }
    }

    #[Route('/{id}/order/{direction}', name: 'listing_order', methods: ['POST'])]
    public function order($direction, Request $request, Listing $listing, OrderService $orderService): Response
    {
        if ($request->isXmlHttpRequest()) {
            if (!$this->getUser() || $this->getUser()->getId() !== $listing->getPage()->getUser()->getId()) {
                return $this->json([
                    'success' => false,
                    'msg'     => Constant::FORBIDDEN
                ], 403);
            }

            $orderService->handleUpAndDownPosition($listing, $listing->getPage()->getListings()->toArray(), $direction);
            $this->getDoctrine()->getManager()->refresh($listing->getPage());
            $orderService->refreshOrder($listing->getPage()->getListings());

            return $this->json([
                'success'   => true
            ]);
        }
    }
}
