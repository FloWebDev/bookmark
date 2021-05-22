<?php

namespace App\Form;

use App\Entity\Page;
use App\Constant\Constant;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'       => 'Titre',
                'attr'        => ['placeholder' => 'Titre'],
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
            ->add('position', ChoiceType::class, [
                'label'    => 'Position',
                'mapped'   => false,
                'choices'  => [
                    'Fin du menu'   => 'end',
                    'Début du menu' => 'start'
                ],
            ])->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $page = $event->getData();
                    $form = $event->getForm();

                    // Dans le cas d'un update
                    if (!is_null($page->getId())) {
                        $form->remove('position');
                    }
                }
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
