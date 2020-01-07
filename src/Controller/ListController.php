<?php

namespace App\Controller;

use App\Entity\Lists;
use App\Service\Serializer\SerializerService;
use App\Util\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ListsRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListController extends AbstractFOSRestController
{
    private $listRepo;
    private $entityManager;
    private $serializer;
    private $cache;

    public function __construct(ListsRepository $listRepo, EntityManagerInterface $entityManager, SerializerService $serializer, CacheInterface $redisCache)
    {
        $this->listRepo = $listRepo;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer->init();
        $this->cache = $redisCache;
    }

    /**
     * @Route("/api/list", name="list", methods={"GET"})
     */
    public function getAllLists()
    {
        $results = $this->cache->cache('all-lists', 120, ['all-cached-values', 'all-lists'], function(){

            return $results = $this->listRepo->findAll();

        });

        if($results)
        {
            $view = $this->view($results, Response::HTTP_OK);
        }
        else
        {
            $view = $this->view($results, 204);
        }

        return $this->handleView($view);
    }

    /**
     * @RequestParam(
     *   name="name",
     *   nullable=false
     * )
     * 
     * @Route("/api/list", name="list_add", methods={"POST"})
     */
    public function add(ParamFetcher $paramFetcher)
    {
        $list = new Lists();

        //make service to handle this, just send the name of the fields and it will return array of values.
        $name = $paramFetcher->get('name');

        if($name){

            $list->setTitle($name);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            $response = [
                'success' => 'List has been added successfully!',
                'list' => $list
            ];

            $this->cache->invalidateCache(['all-lists']);

            $view = $this->view($response, 200);

            return $this->handleView($view);
        }

        // $view = $this->view(new JsonResponse($data), 400);

        // return $this->handleView($view);
        
        return new response(json_encode(['name_field' => 'name is required']));
    }
}
