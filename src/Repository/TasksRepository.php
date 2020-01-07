<?php

namespace App\Repository;

use App\Entity\Tasks;
use App\Entity\Lists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Tasks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tasks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tasks[]    findAll()
 * @method Tasks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasks::class);
    }

    /**
     * @return Tasks[] Returns an array of Tasks objects
     */
    public function getAllUnnotifiedTasks()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.is_notified = 0')
            ->orderBy('t.run_at', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Tasks[] Returns an array of Tasks objects
     */
    public function getAllByUser($id)
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->join(Lists::class, 'l', 'WITH', 't.list = l.id')
            ->where('l.user = ?1')
            ->orderBy('t.run_at', 'DESC')
            ->setParameter(1, $id)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Tasks
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
