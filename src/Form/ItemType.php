<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\Listing;
use App\Repository\ListingRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('title')
            ->add('url')
            ->add('note')
            ->add('listing', EntityType::class, [
                'label'         => 'Liste',
                'class'         => Listing::class,
                'query_builder' => function (ListingRepository $pr) {
                    return $pr->createQueryBuilder('l')
                    ->join('l.page', 'p')
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
            'data_class' => Item::class,
        ]);
    }
}
