<?php

namespace App\Form;

use App\Constant\Constant;
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
            ->add('url', UrlType::class, [
                'label'       => 'URL',
                'attr'        => ['placeholder' => 'https://www.google.com/'],
                'constraints' => [
                    new Regex([
                        'pattern' => "/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/",
                        'match'   => true,
                        'message' => Constant::CONSTRAINT_MESSAGE_URL_FORMAT
                    ])
                ]
            ])
            ->add('title', TextType::class, [
                'label'       => 'Titre',
                'attr'        => ['placeholder' => 'Titre'],
                'constraints' => [
                    new NotBlank([
                        'message' => Constant::CONSTRAINT_MESSAGE_NOT_BLANK
                    ]),
                    new Length([
                        'max'        => 500,
                        'maxMessage' => Constant::CONSTRAINT_MESSAGE_MAX_LENGTH . '{{ limit }}'
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
                        'maxMessage' => Constant::CONSTRAINT_MESSAGE_MAX_LENGTH . '{{ limit }}'
                    ])
                ]
            ])->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) {
                    $item = $event->getData();
                    $form = $event->getForm();
                    $this->pageId = $event->getForm()->getConfig()->getOption('page_id');

                    // Dans le cas d'un update
                    if (!is_null($item->getId())) {
                        $form->remove('position');
                    }

                    if (!is_null($this->pageId)) {
                        $form->add('listing', EntityType::class, [
                            'label'         => 'Liste',
                            'class'         => Listing::class,
                            'query_builder' => function (ListingRepository $pr) {
                                return $pr->createQueryBuilder('l')
                                ->join('l.page', 'p')
                                ->where('p.user = ' . $this->security->getUser()->getId())
                                ->andWhere('p.id = ' . $this->pageId)
                                ->orderBy('p.z', 'ASC', )
                                ->addOrderBy('l.z', 'ASC');
                            },
                            'choice_label' => function ($listing) {
                                return $listing->getPage()->getTitle() . ' - ' . $listing->getTitle();
                            },
                            'constraints'  => [
                                new NotBlank([
                                    'message' => Constant::CONSTRAINT_MESSAGE_NOT_BLANK
                                ])
                            ]
                        ]);
                    }
                }
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
            'page_id'    => null
        ]);
    }
}
