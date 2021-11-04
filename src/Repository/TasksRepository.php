<?php

namespace App\Repository;

use App\Entity\Tasks;
use App\Entity\Lists;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

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
     * @return mixed
     */
    public function getAllUnnotifiedTasks($id)
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->join(Lists::class, 'l', 'WITH', 't.list = l.id')
            ->where('l.user = ?1')
            ->andWhere('t.is_notified = 0')
            ->andWhere('t.is_done = 0')
            ->orderBy('t.run_at', 'ASC')
            ->setParameter(1, $id)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return QueryBuilder
     */
    public function getAllByRunAtQuery($id, $order)
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->join(Lists::class, 'l', 'WITH', 't.list = l.id')
            ->where('l.user = ?1')
            ->orderBy('t.run_at', $order)
            ->setParameter(1, $id)
        ;
    }

    /**
     * @return QueryBuilder
     */
    public function getAllByPriorityQuery($id, $order)
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->join(Lists::class, 'l', 'WITH', 't.list = l.id')
            ->where('l.user = ?1')
            ->orderBy('t.priority', $order)
            ->setParameter(1, $id)
        ;
    }

    /**
     * @param int $userId
     * @param array $criteria
     *
     * @return Query
     */
    public function getTasksByUser(int $userId, array $criteria)
    {
        $queryBuilder = $this->createQueryBuilder('task');
        
        $queryBuilder->andWhere('task.user = :user')
            ->andWhere($queryBuilder->expr()->eq('task.isDone', ':isDone'))
            ->setParameter('user', $userId)
            ->setParameter('isDone', false)
        ;

        $sort = $criteria['sort'] ?? 'priority';
        $order = $criteria['order'] ?? 'desc';
        $queryBuilder->orderBy("task.$sort", $order);

        return $queryBuilder->getQuery();
    }
}
