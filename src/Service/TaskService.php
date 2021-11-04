<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\TasksRepository;
use App\Service\FileUploader;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\ValidationService;

/**
 * UserService
 */
class TaskService extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var TasksRepository */
    private $taskRepository;

    /** @var FileUploader */
    private $fileUploader;

    public function __construct(
        FileUploader $fileUploader,
        EntityManagerInterface $entityManager,
        TasksRepository $taskRepository
    ) {
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
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
}
