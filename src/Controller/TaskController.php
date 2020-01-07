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
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Serializer\SerializerService;

class TaskController extends AbstractFOSRestController
{
    private $entityManager;
    private $tasksRepo;
    private $serializer;
    private $cache;

    public function __construct(EntityManagerInterface $entityManager, TasksRepository $tasksRepo, SerializerService $serializer, CacheInterface $redisCache)
    {
        $this->entityManager = $entityManager;
        $this->tasksRepo = $tasksRepo;
        $this->serializer = $serializer->init();
        $this->cache = $redisCache;
    }

    /**
     * @Route("/api/task", name="all_tasks", methods={"GET"})
     */
    public function getAllTasks()
    {
        $results = $this->cache->cache('all-tasks', 120, ['all-cached-values', 'all-tasks'], function(){

            return $results = $this->tasksRepo->findAll();

        });
        
        if($results)
        {
            $statusCode = 200;
        }
        else
        {   
            $statusCode = 204;
        }

        // var_dump($results);
        // $response = $this->serializer->serialize($results, 'json');
        // return new Response($results, $statusCode);
        $view = $this->view($results, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/task/unnotifed", name="unnotifed_tasks", methods={"GET"})
     */
    public function getUnnotifiedTasks()
    {
        $results = $this->cache->cache('unnotified-tasks', 120, ['all-cached-values', 'unnotified-tasks'], function(){

            return $results = $this->tasksRepo->getAllUnnotifiedTasks();
            
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
    public function updateNotifyStatus($id)
    {
        $obj = $this->tasksRepo->find($id);
        $obj->setIsNotified(true);
        $this->entityManager->flush();

        $this->cache->invalidateCache(['unnotified-tasks']);

        $view = $this->view([], 204);
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
    public function add(ParamFetcher $paramFetcher)
    {
        $tasks = new Tasks();

        //make service to handle this, just send the name of the fields and it will return array of values.
        $title = $paramFetcher->get('title');
        $note = $paramFetcher->get('note');
        $list = $paramFetcher->get('list');
        $priority = $paramFetcher->get('priority');
        $runAt = $paramFetcher->get('run_at');
        
        $lists = $this->getDoctrine()->getRepository(Lists::class)->find($list);

        if($title)
        {
            $tasks->setTitle($title);
            $tasks->setNote($note);
            $tasks->setList($lists);
            $tasks->setPriority($priority);
            $tasks->setRunAt($runAt);

            // $lists->addTask($tasks);

            // $this->entityManager->persist($listObject);
            $this->entityManager->persist($tasks);
            $this->entityManager->flush();
            
            $response = [
                'success' => 'task has been added successfully!',
                // 'task' => $tasks,
            ];
            
            // check if the tag or the key exists first before invalidating the cache
            $this->cache->invalidateCache(['all-tasks', 'unnotified-tasks']);
            
            $view = $this->view($response, 200);
            
            return $this->handleView($view);
        }
        else
        {
            return new response(json_encode(['title_field' => 'title is required']));
        }

        $view = $this->view(new JsonResponse($data), 400);

        return $this->handleView($view);
    
    }
}
