<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Article;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */

    public function findButOne($categorie, $slug)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.categorie', 'c')
            ->andWhere('a.categorie = :val')
            ->setParameter('val', $categorie)
            ->andWhere('a.slug != :slug')
            ->setParameter('slug', $slug)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Return an array of products from a search
     * @return Article[]
     */
    public function findBySearch(Search $search)
    {
        $query = $this->createQueryBuilder('a')
            ->select('c', 'a')
            ->join('a.categorie', 'c');

        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id in (:categories)')
                ->setParameter('categories', $search->categories);
        }

        if (!empty($search->string)) {
            $query = $query
                ->andWhere('a.name like :string')
                ->setParameter('string', "%$search->string%");
        }
        return $query->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
