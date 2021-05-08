<?php

namespace App\Form;

use App\Entity\Page;
use App\Entity\Listing;
use App\Repository\PageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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
                        'message' => 'Ce champ ne doit pas être vide'
                    ]),
                    new Length([
                        'max'        => 60,
                        'maxMessage' => 'Nombre de caractères maximum attendus : {{ limit }}'
                    ])
                ]
            ])
            ->add('z', IntegerType::class, [
                'label'         => 'Ordre dans la liste',
                'attr'          => [
                    'min' => 1,
                    'max' => 100
                ],
                'empty_data'        => 1,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas être vide'
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Listing::class
        ]);
    }
}
