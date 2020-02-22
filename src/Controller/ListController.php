<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\Lists;
use App\Service\Serializer\SerializerService;
use App\Util\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ListsRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\UserService;
use App\Service\ValidationService;

class ListController extends BaseController
{
    private $listRepo;
    private $entityManager;
    private $serializer;
    private $cache;
    private $user;

    public function __construct(ListsRepository $listRepo, EntityManagerInterface $entityManager, SerializerService $serializer, CacheInterface $redisCache, UserService $user)
    {
        $this->listRepo = $listRepo;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer->init();
        $this->cache = $redisCache;
        $this->user = $user;
    }

    /**
     * @Route("/api/list", name="list", methods={"GET"})
     */
    public function getAllLists()
    {
        $results = $this->cache->cache('all-lists', 120, ['all-cached-values', 'all-lists'], function(){

            return $results = $this->listRepo->findBy(['user' => $this->user->getCurrentUser()->getId()]);

        });

        if(!$results){
            return $this->baseView(null, 204);
        }

        return $this->baseView($results);
    }

    /**
     * @RequestParam(
     *   name="title",
     *   nullable=false
     * )
     * 
     * @Route("/api/list", name="list_add", methods={"POST"})
     */
    public function add(ParamFetcher $paramFetcher, ValidatorInterface $validator, ValidationService $validationService)
    {
        $list = new Lists();

        //make service to handle this, just send the name of the fields and it will return array of values.
        $title = $paramFetcher->get('title');
        
        $list->setTitle(trim($title));
        $list->setUser($this->user->getCurrentUser());
        
        $errors = $validator->validate($list);

        if(count($errors) > 0){

            $violations = $validationService->identifyErrors($errors);

            return $this->baseView($violations, 400);
        }

        $this->entityManager->persist($list);
        $this->entityManager->flush();

        $response = [
            'success' => 'List has been added successfully!',
            'list' => $list
        ];

        $this->cache->invalidateCache(['all-lists']);

        return $this->baseView($response);
    }
}
