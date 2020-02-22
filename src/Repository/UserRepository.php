<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Tasks;
use App\Entity\Lists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($registry, User::class);
    }

    public function GetUsersWeeklyInfo()
    {
        // $rsm = new ResultSetMapping();

        // $rsm->addEntityResult(User::class, 'u');
        // $rsm->addFieldResult('u', 'id', 'id');
        // $rsm->addFieldResult('u', 'email', 'email');
        // $rsm->addJoinedEntityResult(Lists::class , 'l', 'u', 'lists');
        // $rsm->addFieldResult('l', 'list_id', 'id');
        // $rsm->addJoinedEntityResult(Tasks::class , 't', 'l', 'tasks');
        // $rsm->addFieldResult('t', 'task_id', 'id');
        // $rsm->addScalarResult('t', 'tasks_count', 'id');
        // $rsm->addScalarResult('t', 'done_tasks', 'id');

        $conn = $this->entityManager->getConnection();

        $query = 
            'SELECT
                u.email,
                COUNT(t.id) AS tasks_count, 
                (
                    SELECT COUNT(t.id)
                    FROM tasks t
                    JOIN lists l ON l.id = t.list_id
                    WHERE u.id = l.user_id
                    AND t.is_done = 1
                ) done_tasks
            FROM user u
            JOIN lists l ON l.user_id = u.id
            JOIN tasks t ON t.list_id = l.id
            WHERE date(t.created_at) >= date(now() - INTERVAL 1 week)
            GROUP BY u.id'
            ;

        $sql = $conn->prepare($query);
        $sql->execute();

        return $sql->fetchAll();

        // return $query->getResult();
    }
}
