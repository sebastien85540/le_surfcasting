<?php

namespace App\Repository;

use App\Entity\Article;
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

    public function findPage (int $page, int $itemPerPage){
        $dql = <<<DQL
SELECT a
FROM App\Entity\Article a
LEFT JOIN a.comments cs
DQL;
        /* si 3 éléments par pages
         pour page 1, le premier élément est 0
         pour page 2, le premier élément est 3
         pour page 3, le premier élément est 6*/
        $start = ($page - 1) * $itemPerPage;
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setFirstResult($start)
            ->setMaxResults($itemPerPage);
        return new Paginator($query, true);

    }

    /**
     * @param int $value
     * @return int|mixed|string
     */
    public function findByMaxView(int $value)
    {
        return $this->createQueryBuilder('a')

            ->orderBy('a.id', 'DESC')
            ->setMaxResults($value)
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

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
