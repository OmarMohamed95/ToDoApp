<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\Serializer\SerializerService;
use App\Service\FileUploader;
use Symfony\Contracts\Cache\ItemInterface;
use App\Util\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractFOSRestController
{
    private $categoryRepo;
    private $entityManager;
    private $serializer;
    // private $uploadsBaseUrl;

    public function __construct(CategoryRepository $categoryRepo, EntityManagerInterface $entityManager, SerializerService $serializer)
    {
        $this->categoryRepo = $categoryRepo;
        $this->entityManager = $entityManager;

        $this->serializer = $serializer->init();

        // $this->uploadsBaseUrl = $uploads_base_url;
    }

    /**
     * @Route("/api/category", name="category", methods={"GET"})
     */
    public function getAllCategories(CacheInterface $redisCache)
    {
        $results = $redisCache->cache('all-categories', 120, ['all-categories'], function(){

            return $results = $this->categoryRepo->findAll();

        });

        // return new Response(json_encode($results));

        $view = $this->view($results, Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @RequestParam(
     *   name="name",
     *   nullable=false
     * )
     * 
     * @FileParam(name="image", image=true, default=NULL, nullable=true)
     * 
     * @Route("/api/category", name="category_add", methods={"POST"})
     */
    public function add(ParamFetcher $paramFetcher, FileUploader $fileUploader)
    {
        $category = new Category();

        $name = $paramFetcher->get('name');
        $image = $paramFetcher->get('image');

        if($name){

            if($image)
            {
                $fileUploader->setDirectoryName('category');
                $fileName = $fileUploader->upload($image)->getFileName();

                $category->setImage($fileName);
            }
            $category->setName($name);

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            // $response = $this->serializer->serialize($category, 'json');

            $response = [
                'success' => 'Category has been added successfully!',
                'category' => $category
            ];

            /* -------------------------------------------------- invalidate the cache ----------------------------------  */

            $view = $this->view($response, 200);

            return $this->handleView($view);

            // return new Response($response, 200);
            
            // return new Response(json_encode([
            //     'success' => 'Category has been added successfully!',
            //     'obj' => $response
            // ]));
        }

        // $view = $this->view(new JsonResponse($data), 400);

        // return $this->handleView($view);
        
        return new response(json_encode(['name_field' => 'name is required']));
    }
}
