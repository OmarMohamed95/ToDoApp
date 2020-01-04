<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;

class HomeController extends AbstractFOSRestController
{
    /**
     * @Route("/{vueRouting}", name="home_index", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).*"})
     */
    public function index(string $vueRouting = '/')
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/api/test", name="home_test", methods={"GET"})
     */
    public function test()
    {
        return $this->json([
            'name' => 'omar mohamed',
            'age' => 24
        ]);
    }
}
