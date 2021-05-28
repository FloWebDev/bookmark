<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Util\MailService;

use App\Constant\Constant;
use App\Util\CaptchaService;
use App\Repository\UserRepository;
use App\Validator\CaptchaConstraint;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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

        $form = $this->createForgotPasswordForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userRepository->findOneBy([
                'username' => $data['username'],
                'email'    => $data['email']
            ]);

            if (is_null($user)) {
                $form->addError(new FormError(Constant::ERROR_NO_MATCHING_USER));
                return $this->render('user/forgot_password.html.twig', [
                    'captcha' => $captchaService->createCaptcha(),
                    'form'    => $form->createView()
                ]);
            }

            $newPassword = uniqid();
            $user->setPassword($encoder->encodePassword($user, $newPassword));
            $this->getDoctrine()->getManager()->flush();

            (array) $res = $emailService->sendEmail($user->getEmail(), Constant::EMAIL_FORGOT_PASSWORD_SUBJECT, str_replace('[NEW_PASSWORD]', $newPassword, Constant::EMAIL_FORGOT_PASSWORD_TEXT), str_replace('[NEW_PASSWORD]', $newPassword, Constant::EMAIL_FORGOT_PASSWORD_HTML));

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

    private function createForgotPasswordForm()
    {
        return $this->createFormBuilder(null, ['method' => 'POST'])
            ->add('username', TextType::class, [
                'label'       => 'Identifiant (*)',
                'attr'        => ['placeholder' => 'Identifiant'],
                'constraints' => [
                    new NotBlank([
                        'message' => Constant::CONSTRAINT_MESSAGE_NOT_BLANK
                    ]),
                    new Length([
                        'max'        => 60,
                        'maxMessage' => Constant::CONSTRAINT_MESSAGE_MAX_LENGTH . '{{ limit }}'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email (*)',
                'help'  => Constant::HELP_FORGOT_PASSWORD_MESSAGE,
                'attr'  => [
                    'placeholder' => 'exemple@gmail.com'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => Constant::CONSTRAINT_MESSAGE_NOT_BLANK
                    ]),
                    new Email([
                        'mode'    => 'loose',
                        'message' => Constant::CONSTRAINT_MESSAGE_INVALID_EMAIL
                    ]),
                    new Length([
                        'min'        => 5,
                        'max'        => 250,
                        'minMessage' => Constant::CONSTRAINT_MESSAGE_MIN_LENGTH . '{{ limit }}',
                        'maxMessage' => Constant::CONSTRAINT_MESSAGE_MAX_LENGTH . '{{ limit }}'
                    ])
                ]
            ])->add('captcha', IntegerType::class, [
                'label'       => Constant::CAPTCHA_LABEL,
                'mapped'      => false,
                'constraints' => [
                    new NotBlank([
                        'message' => Constant::CONSTRAINT_MESSAGE_NOT_BLANK
                    ]),
                    new Length([
                        'min'        => 4,
                        'max'        => 4,
                        'minMessage' => Constant::CONSTRAINT_MESSAGE_MIN_LENGTH . '{{ limit }}',
                        'maxMessage' => Constant::CONSTRAINT_MESSAGE_MAX_LENGTH . '{{ limit }}'
                    ]),
                    new CaptchaConstraint()
                ]
            ])->getForm();
    }
}
