<?php

namespace App\Service;

use App\Entity\Tasks;
use App\Entity\User;
use App\Exception\UnauthorizedTaskUser;
use App\Repository\TasksRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Task Service
 */
class TaskService extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var TasksRepository */
    private $taskRepository;

    /** @var FileUploader */
    private $fileUploader;

    /** @var TokenStorage */
    private $tokenStorage;

    public function __construct(
        FileUploader $fileUploader,
        EntityManagerInterface $entityManager,
        TasksRepository $taskRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get all task of a user
     *
     * @param User $user
     * @param array $criteria
     *
     * @return mixed
     */
    public function getTasksByUser(User $user, array $criteria)
    {
        return $this->taskRepository->getTasksByUser($user->getId(), $criteria);
    }

    /**
     * Get unnotified task of a user
     *
     * @param User $user
     *
     * @return mixed
     */
    public function getUnnotifiedTasks(User $user)
    {
        return $this->taskRepository->getAllUnnotifiedTasks($user->getId());
    }

    /**
     * Update notify status of a task
     *
     * @param Tasks $task
     *
     * @return mixed
     */
    public function updateNotifyStatus(Tasks $task)
    {
        $isTaskOwner = $this->isTaskOwner($task);

        if (!$isTaskOwner) {
            return;
        }

        $task->setIsNotified(true);
        $this->entityManager->flush();
    }

    /**
     * Check if the current user is the task owner
     *
     * @param Tasks $task
     *
     * @return bool
     * @throws UnauthorizedTaskUser
     */
    public function isTaskOwner(Tasks $task)
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token ||
            ($token->getUser() instanceof User && $token->getUser()->getId() !== $task->getUser()->getId())
        ) {
            throw new UnauthorizedTaskUser("Unauthorized User");
        }

        return true;
    }
}
