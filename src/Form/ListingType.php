<?php

namespace App\Form;

use App\Entity\Page;
use App\Entity\Listing;
use App\Constant\Constant;
use App\Repository\PageRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ListingType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
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
                        'maxMessage' => 'Nombre de caractères maximum attendus : {{ limit }}'
                    ])
                ]
            ])
            ->add('page', EntityType::class, [
                'label'         => 'Page',
                'class'         => Page::class,
                'query_builder' => function (PageRepository $pr) {
                    return $pr->createQueryBuilder('p')
                    ->where('p.user = ' . $this->security->getUser()->getId())
                                ->orderBy('p.z', 'ASC');
                },
                'choice_label' => 'title'
            ])
            ->add('position', ChoiceType::class, [
                'label'    => 'Position',
                'mapped'   => false,
                'choices'  => [
                    'Fin de page'   => 'end',
                    'Début de page' => 'start'
                ],
            ])->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $listing = $event->getData();
                    $form = $event->getForm();

                    // Dans le cas d'un update
                    if (!is_null($listing->getId())) {
                        $form->remove('page')
                        ->remove('position');
                    }
                }
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Listing::class
        ]);
    }
}
