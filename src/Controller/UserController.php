<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Util\MailService;

use App\Constant\Constant;
use App\Util\CaptchaService;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    #[Route('users', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('sign-up', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordEncoderInterface $encoder, CaptchaService $captchaService): Response
    {
        $this->denyAccessUnlessGranted('IS_ANONYMOUS');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRole('ROLE_USER');
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                Constant::SUCCESS_SIGN_UP
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/new.html.twig', [
            'user'    => $user,
            'captcha' => $captchaService->createCaptcha(),
            'form'    => $form->createView(),
        ]);
    }

    #[Route('user/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->getUser()->getId() !== $user->getId() && $this->getUser()->getRole() !== 'ROLE_ADMIN') {
            $this->addFlash(
                'danger',
                Constant::FORBIDDEN
            );
            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()->getId() !== $user->getId() && $this->getUser()->getRole() !== 'ROLE_ADMIN') {
                $this->addFlash(
                    'danger',
                    Constant::FORBIDDEN
                );
                return $this->redirectToRoute('dashboard');
            }

            if (!is_null($form->get('password')->getData())) {
                $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                Constant::SUCCESS_ACTION
            );

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('user/delete/{id}', name: 'user_delete', methods: ['GET'])]
    public function delete(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Un compte utilisateur ADMIN ne peut pas être supprimé
        if ($user->getRole() === 'ROLE_ADMIN') {
            $this->addFlash(
                'danger',
                Constant::UNAUTHORIZED
            );
            return $this->redirectToRoute('dashboard');
        }
        
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->query->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        $this->addFlash(
            'success',
            Constant::SUCCESS_ACTION
        );

        return $this->redirectToRoute('dashboard');
    }

    #[Route('/forgot-password', name: 'forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, UserRepository $userRepository, MailService $emailService, UserPasswordEncoderInterface $encoder, CaptchaService $captchaService): Response
    {
        $this->denyAccessUnlessGranted('IS_ANONYMOUS');

        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'context' => 'forgot_password'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userTarget = $userRepository->findOneBy([
                'username' => $user->getUsername(),
                'email'    => $user->getEmail()
            ]);

            if (is_null($userTarget)) {
                $form->addError(new FormError(Constant::ERROR_NO_MATCHING_USER));
                return $this->render('user/forgot_password.html.twig', [
                    'captcha' => $captchaService->createCaptcha(),
                    'form'    => $form->createView()
                ]);
            }

            $newPassword = uniqid();
            $userTarget->setPassword($encoder->encodePassword($userTarget, $newPassword));
            $this->getDoctrine()->getManager()->flush();

            (array) $res = $emailService->sendEmail($userTarget->getEmail(), Constant::EMAIL_FORGOT_PASSWORD_SUBJECT, str_replace('[NEW_PASSWORD]', $newPassword, Constant::EMAIL_FORGOT_PASSWORD_TEXT), str_replace('[NEW_PASSWORD]', $newPassword, Constant::EMAIL_FORGOT_PASSWORD_HTML));

            $this->addFlash(
                $res['success'] ? 'success' : 'danger',
                $res['message']
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/forgot_password.html.twig', [
            'captcha' => $captchaService->createCaptcha(),
            'form'    => $form->createView()
        ]);

        return $this->redirectToRoute('dashboard');
    }
}
