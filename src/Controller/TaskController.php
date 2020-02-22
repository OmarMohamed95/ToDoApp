<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tasks;
use App\Entity\Lists;
use App\Repository\TasksRepository;
use App\Util\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Serializer\SerializerService;
use App\Service\UserService;
use App\Service\ValidationService;
use Knp\Component\Pager\PaginatorInterface;

class TaskController extends AbstractFOSRestController
{
    private $entityManager;
    private $tasksRepo;
    private $serializer;
    private $cache;
    private $user;

    public function __construct(EntityManagerInterface $entityManager, TasksRepository $tasksRepo, SerializerService $serializer, CacheInterface $redisCache, UserService $user)
    {
        $this->entityManager = $entityManager;
        $this->tasksRepo = $tasksRepo;
        $this->serializer = $serializer->init();
        $this->cache = $redisCache;
        $this->user = $user;
    }

    /**
     * @Route("/api/tasks", name="all_tasks", methods={"GET"})
     */
    public function getAllTasks(Request $request, PaginatorInterface $paginator)
    {
        $countPerPage = 10;
        
        $pageNumber = $request->query->getInt('page', 1);
        $sort = $request->query->get('sort', 'run_at');
        $order = $request->query->get('order', 'desc');
        
        $cacheKey = 'tasks-'. $sort . '-' . $order . '-' . $pageNumber;
        
        $results = $this->cache->cache($cacheKey, 120, ['all-cached-values', 'all-tasks'], function() use ($paginator, $pageNumber, $countPerPage, $sort, $order){

            if($sort === 'run_at')
            {
                $queryBuilder = $this->tasksRepo->getAllByRunAtQuery($this->user->getCurrentUser()->getId(), $order);
            }
            elseif($sort === 'priority')
            {
                $queryBuilder = $this->tasksRepo->getAllByPriorityQuery($this->user->getCurrentUser()->getId(), $order);
            }

            $pagination = $paginator->paginate(
                $queryBuilder,
                $pageNumber,
                $countPerPage
            );

            return $pagination;

        });
        
        if($results)
        {
            $statusCode = 200;
        }
        else
        {   
            $statusCode = 204;
        }

        // $view = $this->view($pagination, 200);
        $view = $this->view($results, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/task/unnotifed", name="unnotifed_tasks", methods={"GET"})
     */
    public function getUnnotifiedTasks()
    {
        $results = $this->cache->cache('unnotified-tasks', 120, ['all-cached-values', 'unnotified-tasks'], function(){

            return $results = $this->tasksRepo->getAllUnnotifiedTasks($this->user->getCurrentUser()->getId());
            
        });
        
        if($results)
        {
            $statusCode = 200;
        }
        else
        {   
            $statusCode = 204;
        }

        $view = $this->view($results, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/task/notified/{id}", name="update_notify_status", methods={"PATCH"})
     */
    public function updateNotifyStatus(int $id)
    {
        $obj = $this->tasksRepo->find($id);
        $obj->setIsNotified(true);
        $this->entityManager->flush();

        $this->cache->invalidateCache(['unnotified-tasks']);

        $view = $this->view([], 204);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/task/{id}", name="task_by_id", methods={"GET"})
     */
    public function getTaskById(int $id)
    {

        $results = $this->tasksRepo->findOneBy(['id' => $id]);

        
        if($results)
        {
            $statusCode = 200;
        }
        else
        {   
            $statusCode = 204;
        }

        $view = $this->view($results, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/task/{id}", name="edit_task", methods={"PUT"})
     * 
     * @RequestParam(name="title", nullable=true)
     * @RequestParam(name="note", nullable=true)
     * @RequestParam(name="list", nullable=true)
     * @RequestParam(name="priority", nullable=true)
     * @RequestParam(name="run_at", nullable=true)
     */
    public function edit(int $id, ParamFetcher $paramFetcher, ValidatorInterface $validator, ValidationService $validationService)
    {
        //make service to handle this, just send the name of the fields and it will return array of values.
        $title = $paramFetcher->get('title');
        $note = $paramFetcher->get('note');
        $list = $paramFetcher->get('list');
        $priority = $paramFetcher->get('priority');
        $runAt = $paramFetcher->get('run_at');
        
        $task = $this->tasksRepo->find($id);
        $lists = $this->getDoctrine()->getRepository(Lists::class)->find($list);

        if($runAt > date("Y-m-d H:i:s"))
        {
            $task->setIsNotified(false);
            $task->setRunAt($runAt);
        }
        $task->setTitle(trim($title));
        $task->setNote(trim($note));
        $task->setList($lists);
        $task->setPriority($priority);
        $task->setIsDone(false);

        $errors = $validator->validate($task);

        if(count($errors) > 0)
        {
            $violations = $validationService->identifyErrors($errors);

            $view = $this->view($violations, 400);
            return $this->handleView($view);
        }

        $this->entityManager->flush();
        
        $response = [
            'success' => 'task has been updated successfully!',
        ];
        
        // check if the tag or the key exists first before invalidating the cache
        $this->cache->invalidateCache(['all-tasks', 'unnotified-tasks']);
        
        $view = $this->view($response, 200);
        
        return $this->handleView($view);
    }

    /**
     * @Route("/api/task", name="add_task", methods={"POST"})
     * 
     * @RequestParam(name="title", nullable=false)
     * @RequestParam(name="note", nullable=true)
     * @RequestParam(name="list", nullable=false)
     * @RequestParam(name="priority", nullable=true)
     * @RequestParam(name="run_at", nullable=false)
     */
    public function add(ParamFetcher $paramFetcher, ValidatorInterface $validator, ValidationService $validationService)
    {
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

        $errors = $validator->validate($tasks);

        if(count($errors) > 0)
        {
            $violations = $validationService->identifyErrors($errors);

            $view = $this->view($violations, 400);
            return $this->handleView($view);
        }

        $this->entityManager->persist($tasks);
        $this->entityManager->flush();
        
        $response = [
            'success' => 'task has been added successfully!',
        ];
        
        // check if the tag or the key exists first before invalidating the cache
        $this->cache->invalidateCache(['all-tasks', 'unnotified-tasks']);
        
        $view = $this->view($response, 200);
        
        return $this->handleView($view);
    }
}
