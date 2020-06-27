<?php

namespace App\Repository;

use App\Entity\QuestionChoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuestionChoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionChoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionChoice[]    findAll()
 * @method QuestionChoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionChoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionChoice::class);
    }

    // /**
    //  * @return QuestionChoice[] Returns an array of QuestionChoice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuestionChoice
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
