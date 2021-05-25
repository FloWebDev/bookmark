<?php

namespace App\Repository;

use App\Entity\Page;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * Permet d'obtenir la page associée à un utilisateur en fonction de son slug et de l'ordre d'affichage de la page
     * 
     * @param string $slug Le slug de l'utilisateur
     * @param int $z L'ordre d'affichage de la page
     *
     * @return mixed La page concernée ou null
     */
    public function findOneBySlugAndOrder(string $slug, int $z): ?Page
    {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->where('u.slug = :slug')
            ->andWhere('p.z = :z')
            ->setParameter('slug', $slug)
            ->setParameter('z', $z)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Page[] Returns an array of Page objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Page
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
