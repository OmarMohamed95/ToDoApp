<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tasks;
use App\Entity\Lists;
use App\Exception\UnauthorizedTaskUser;
use App\Repository\TasksRepository;
use App\Util\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use App\Service\Serializer\SerializerService;
use App\Service\TaskService;
use App\Service\ValidationService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * TaskController
 */
class TaskController extends BaseController
{
    /** @var EntityManagerInterface */
    private $entityManager;
    
    /** @var TasksRepository */
    private $tasksRepo;

    /** @var SerializerService */
    private $serializer;

    /** @var CacheInterface */
    private $cache;

    /** @var TranslatorInterface */
    private $translator;

    /** @var TaskService */
    private $taskService;

    public function __construct(
        EntityManagerInterface $entityManager,
        TasksRepository $tasksRepo,
        SerializerService $serializer,
        CacheInterface $redisCache,
        TranslatorInterface $translator,
        TaskService $taskService
    ) {
        $this->entityManager = $entityManager;
        $this->tasksRepo = $tasksRepo;
        $this->serializer = $serializer->init();
        $this->cache = $redisCache;
        $this->translator = $translator;
        $this->taskService = $taskService;
    }

    /**
     * Get all tasks
     *
     * @Route("/api/tasks", name="all_tasks", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function getAllTasks(
        Request $request,
        PaginatorInterface $paginator
    ) {
        $criteria = [
            'sort' => $request->query->get('sort', 'run_at'),
            'order' => $request->query->get('order', 'desc')
        ];
        
        $tasksQuery = $this->taskService->getTasksByUser($this->getUser(), $criteria);

        $pagination = $paginator->paginate(
            $tasksQuery,
            $request->query->getInt('page', 1),
            10
        );

        if (!$pagination->getTotalItemCount()) {
            return $this->respondNoContent();
        }

        return $this->respond($pagination);
    }

    /**
     * Get unnotified tasks
     *
     * @Route("/api/task/unnotifed", name="unnotifed_tasks", methods={"GET"})
     *
     * @return Response
     */
    public function getUnnotifiedTasks()
    {
        $unnotifiedTasks = $this->taskService->getUnnotifiedTasks($this->getUser());

        if (!$unnotifiedTasks) {
            return $this->respondNoContent();
        }

        return $this->respond($unnotifiedTasks);
    }

    /**
     * Update notify status
     *
     * @Route("/api/task/{task}/notification", name="update_notify_status", methods={"PATCH"})
     *
     * @param Tasks $task
     *
     * @return Response
     */
    public function updateNotifyStatus(Tasks $task)
    {
        try {
            $this->taskService->updateNotifyStatus($task, true);
        } catch (UnauthorizedTaskUser $th) {
            $this->respond(
                [
                    'status_message' => $this->translator->trans('task.errors.unauthorized_user')
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->respond([
            'status_message' => $this->translator->trans('task.messages.successfully_notify_updated')
        ]);
    }

    /**
     * Get task by id
     *
     * @Route("/api/task/{id}", name="task_by_id", methods={"GET"})
     *
     * @param int $id
     *
     * @return Response
     */
    public function getTaskById(int $id)
    {
        $results = $this->tasksRepo->findOneBy(['id' => $id]);

        if (!$results) {
            return $this->respondNoContent();
        }

        return $this->respond($results);
    }

    /**
     * Edit task
     *
     * @Route("/api/task/{id}", name="edit_task", methods={"PUT"})
     *
     * @RequestParam(name="title", nullable=true)
     * @RequestParam(name="note", nullable=true)
     * @RequestParam(name="list", nullable=true)
     * @RequestParam(name="priority", nullable=true)
     * @RequestParam(name="run_at", nullable=true)
     *
     * @param int $id
     * @param ParamFetcher $paramFetcher
     * @param ValidatorInterface $validator
     * @param ValidationService $validationService
     *
     * @return Response
     */
    public function edit(
        int $id,
        ParamFetcher $paramFetcher,
        ValidatorInterface $validator,
        ValidationService $validationService
    ) {
        //make service to handle this, just send the name of the fields and it will return array of values.
        $title = $paramFetcher->get('title');
        $note = $paramFetcher->get('note');
        $list = $paramFetcher->get('list');
        $priority = $paramFetcher->get('priority');
        $runAt = $paramFetcher->get('run_at');
        
        $task = $this->tasksRepo->find($id);
        $lists = $this->getDoctrine()->getRepository(Lists::class)->find($list);

        if ($runAt > date("Y-m-d H:i:s")) {
            $task->setIsNotified(false);
            $task->setRunAt($runAt);
        }

        $task->setTitle(trim($title));
        $task->setNote(trim($note));
        $task->setList($lists);
        $task->setPriority($priority);
        $task->setIsDone(false);

        $errors = $validator->validate($task);

        if (count($errors) > 0) {
            $violations = $validationService->identifyErrors($errors);

            return $this->respond($violations, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();
        
        $response = [
            'success' => 'task has been updated successfully!',
        ];
        
        // check if the tag or the key exists first before invalidating the cache
        $this->cache->invalidateCache(['all-tasks', 'unnotified-tasks']);
        
        return $this->respond($response);
    }

    /**
     * Add new task
     *
     * @Route("/api/task", name="add_task", methods={"POST"})
     *
     * @RequestParam(name="title", nullable=false)
     * @RequestParam(name="note", nullable=true)
     * @RequestParam(name="list", nullable=false)
     * @RequestParam(name="priority", nullable=true)
     * @RequestParam(name="run_at", nullable=false)
     *
     * @param ParamFetcher $paramFetcher
     * @param ValidatorInterface $validator
     * @param ValidationService $validationService
     *
     * @return Response
     */
    public function add(
        ParamFetcher $paramFetcher,
        ValidatorInterface $validator,
        ValidationService $validationService
    ) {
        $tasks = new Tasks();

        //make service to handle this, just send the name of the fields and it will return array of values.
        $title = $paramFetcher->get('title');
        $note = $paramFetcher->get('note');
        $list = $paramFetcher->get('list');
        $priority = $paramFetcher->get('priority');
        $runAt = $paramFetcher->get('run_at');
        
        $lists = $this->getDoctrine()->getRepository(Lists::class)->find($list);

        $tasks->setTitle(trim($title));
        $tasks->setNote(trim($note));
        $tasks->setList($lists);
        $tasks->setPriority($priority);
        $tasks->setRunAt($runAt);
        $tasks->setUser($this->getUser());

        // $errors = $validator->validate($tasks);

        // if (count($errors) > 0) {
        //     $violations = $validationService->identifyErrors($errors);

        //     return $this->baseView($violations, 400);
        // }

        $this->entityManager->persist($tasks);
        $this->entityManager->flush();
        
        $response = [
            'success' => 'task has been added successfully!',
        ];
        
        // check if the tag or the key exists first before invalidating the cache
        $this->cache->invalidateCache(['all-tasks', 'unnotified-tasks']);
        
        return $this->respond($response, Response::HTTP_CREATED);
    }
}
