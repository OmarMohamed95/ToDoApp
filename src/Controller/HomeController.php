<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;

/**
 * Home controller
 */
class HomeController extends BaseController
{
    /**
     * Index view
     *
     * @Route("/{vueRouting}", name="home_index", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).*"})
     *
     * @param string $vueRouting
     */
    public function index(string $vueRouting = '/')
    {
        return $this->render('base.html.twig');
    }
}
