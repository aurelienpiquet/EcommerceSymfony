<?php

namespace App\Repository;

use App\Entity\NewCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NewCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewCollection[]    findAll()
 * @method NewCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewCollection::class);
    }

    // /**
    //  * @return NewCollection[] Returns an array of NewCollection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NewCollection
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
