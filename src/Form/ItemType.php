<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\Listing;
use App\Repository\ListingRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ItemType extends AbstractType
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
                        'message' => 'Ce champ ne doit pas être vide'
                    ]),
                    new Length([
                        'max'        => 500,
                        'maxMessage' => 'Nombre de caractères maximum attendus : {{ limit }}'
                    ])
                ]
            ])
            ->add('url', UrlType::class, [
                'label'       => 'URL',
                'attr'        => ['placeholder' => 'https://www.google.com/'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas être vide'
                    ]),
                    new Regex([
                        'pattern' => "/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/",
                        'match'   => true,
                        'message' => 'L\'URL renseignée n\'est pas valide'
                    ])
                ]
            ])
            ->add('listing', EntityType::class, [
                'label'         => 'Liste',
                'class'         => Listing::class,
                'query_builder' => function (ListingRepository $pr) {
                    return $pr->createQueryBuilder('l')
                    ->join('l.page', 'p')
                    ->where('p.user = ' . $this->security->getUser()->getId())
                    ->orderBy('p.z', 'ASC', )
                    ->addOrderBy('l.z', 'ASC');
                },
                'choice_label' => 'title',
                'constraints'  => [
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas être vide'
                    ])
                ]
            ])
            ->add('position', ChoiceType::class, [
                'label'    => 'Position',
                'mapped'   => false,
                'choices'  => [
                    'Fin de liste'   => 'end',
                    'Début de liste' => 'start'
                ],
            ])
            ->add('note', TextareaType::class, [
                'label'       => 'Note',
                'attr'        => ['rows' => 10],
                'constraints' => [
                    new Length([
                        'max'        => 5000,
                        'maxMessage' => 'Nombre de caractères maximum autorisés : {{ limit }}'
                    ])
                ]
            ])->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) {
                    $item = $event->getData();
                    $form = $event->getForm();

                    // Dans le cas d'un update
                    if (!is_null($item->getId())) {
                        $form->remove('position');
                    }
                }
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}
