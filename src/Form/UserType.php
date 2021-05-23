<?php

namespace App\Form;

use App\Entity\User;
use App\Util\Captcha;
use App\Constant\Constant;
use App\Validator\CaptchaConstraint;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Validator\UniqueCaseInsensitive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    private $security;
    private $options;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->options = $options;
        $builder
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
                    ]),
                    new UniqueCaseInsensitive([
                        'field'        => 'username',
                        'currentValue' => $builder->getData()->getUsername()
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email (*)',
                'help'  => Constant::HELP_EMAIL_MESSAGE,
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
                    new UniqueCaseInsensitive([
                        'field'        => 'email',
                        'currentValue' => $builder->getData()->getEmail()
                    ])
                ]
            ])->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $user = $event->getData();
                    $form = $event->getForm();

                    if (is_null($user->getId()) && empty($this->options['context'])) {
                        // Dans le cas d'un create
                        $form->add('password', RepeatedType::class, [
                            'type'            => PasswordType::class,
                            'invalid_message' => Constant::CONSTRAINT_MESSAGE_CONFIRMATION_PASSWORD,
                            'options'         => ['attr' => ['class' => 'password-field']],
                            'required'        => true,
                            'first_options'   => ['label' => 'Mot de passe (*)'],
                            'second_options'  => ['label' => 'Confirmation mot de passe (*)'],
                            'constraints'     => [
                                new NotBlank([
                                    'message' => Constant::CONSTRAINT_MESSAGE_NOT_BLANK
                                ]),
                                new Length([
                                    'min'        => 5,
                                    'max'        => 64,
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
                        ]);
                    } elseif (is_null($user->getId()) && $this->options['context'] === 'forgot_password') {
                        // Dans le cas d'un mot de passe oublié
                        $form->remove('username')->remove('email')
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
                        ]);
                    } else {
                        // Dans le cas d'un update
                        $form->add('password', RepeatedType::class, [
                            'type'            => PasswordType::class,
                            'invalid_message' => Constant::CONSTRAINT_MESSAGE_CONFIRMATION_PASSWORD,
                            'options'         => ['attr' => ['class' => 'password-field']],
                            'required'        => true,
                            'first_options'   => ['label' => 'Nouveau mot de passe'],
                            'second_options'  => ['label' => 'Confirmation du nouveau mot de passe'],
                            'constraints'     => [
                                new Length([
                                    'min'        => 5,
                                    'max'        => 64,
                                    'minMessage' => Constant::CONSTRAINT_MESSAGE_MIN_LENGTH . '{{ limit }}',
                                    'maxMessage' => Constant::CONSTRAINT_MESSAGE_MAX_LENGTH . '{{ limit }}'
                                ])
                            ]
                        ]);
                    }

                    // Si l'utlisateur est administrateur et qu'il ne s'agit pas de son propre compte
                    if ($this->security->getUser()?->getRole() === 'ROLE_ADMIN' && $this->security->getUser()->getId() !== $user->getId()) {
                        $form->add('role', ChoiceType::class, [
                            'label'   => 'Rôle',
                            'choices' => [
                                'Utilisateur'    => 'ROLE_USER',
                                'Administrateur' => 'ROLE_ADMIN'
                            ],
                            'expanded' => false,
                            'multiple' => false
                        ]);
                    }
                }
            );
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'context'    => null
        ]);
    }
}
