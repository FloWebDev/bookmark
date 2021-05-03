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
    * @return Page[] Returns an array of Page objects
    */
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.z', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Page[] Returns an array of Page objects
    */
    public function refreshOrderZ(int $userId, int $z, string $direction)
    {
        if ($direction === 'UP') {
            // UP
            $sql  = "UPDATE App\Entity\Page p SET p.z = 
        (case when p.z < $z then (p.z - 1) else (p.z + 1) end)
        WHERE p.user = $userId";
        } else {
            // DOWN
            $sql  = "UPDATE App\Entity\Page p SET p.z = 
        (case when p.z <= $z then (p.z - 1) else (p.z + 1) end)
        WHERE p.user = $userId";
        }

        $stmt = $this->getEntityManager()->createQuery($sql);
        return $stmt->execute([]);
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
