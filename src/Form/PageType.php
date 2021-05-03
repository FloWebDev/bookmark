<?php

namespace App\Form;

use App\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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
                        'message' => 'Ce champ ne doit pas être vide'
                    ]),
                    new Length([
                        'max'        => 60,
                        'maxMessage' => 'Nombre de caractères maximum attendus : {{ limit }}'
                    ])
                ]
            ])
            ->add('z', IntegerType::class, [
                'label' => 'Ordre dans la liste',
                'attr'  => [
                    'min' => 1,
                    'max' => 100
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas être vide'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
