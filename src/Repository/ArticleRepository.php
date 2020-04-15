<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * 
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findArticlePublishedOrderedByNewest(){
        return $this->createQueryBuilder('a')
            ->where('a.state != false')
            ->orderBy('a.date_published', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findArticlePublishedOrderedByNewestCategory(Category $category){
        return $this->createQueryBuilder('a')
            ->where('a.state != false')
            ->andWhere('a.category = :category')
            ->setParameter('category', $category)
            ->orderBy('a.date_published', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findHome(){
        return $this->createQueryBuilder('a')
            ->where('a.home IS NOT NULL')
            ->andwhere('a.state != false')
            ->orderBy('a.home', 'ASC')
            ->setMaxResults(3)
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
