<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\UserService;
use App\Util\CacheInterface;

/**
 * @Route("/api/")
 */
class AuthController extends AbstractFOSRestController
{
    private $request;
    private $response;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->response = new Response;
    }

    /**
     * @Route("auth/check", name="check_auth", methods={"GET"})
     */
    public function checkIfAuthenticated()
    {
        $refreshToken = $this->request->cookies->get('REFRESH_TOKEN');

        if($refreshToken){
            $response = ['isAuthenticated' => true];
        }
        else
        {
            $response = ['isAuthenticated' => false];
        }
        
        $view = $this->view($response, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("auth/user", name="user", methods={"GET"})
     */
    public function getCurrentUser(UserService $user)
    {
        $currentUser = $user->getCurrentUser(['id' => 'getId', 'username' => 'getUsername']);

        if($currentUser != null)
        {
            $status = 200;
        }
        else
        {
            $status = 204;
        }

        $view = $this->view($currentUser, $status);
        return $this->handleView($view);
    }

    /**
     * @Route("logout", name="logout", methods={"GET"})
     */
    public function logout(CacheInterface $redisCache)
    {
        $redisCache->invalidateCache(['all-cached-values']);

        $this->response->headers->clearCookie('BEARER');
        $this->response->headers->clearCookie('REFRESH_TOKEN');
        $this->response->send();

        // $refreshToken = $this->request->cookies->get('REFRESH_TOKEN');
        
        $view = $this->view(['message' => 'logged out successfully'], 200);
        return $this->handleView($view);
    }
}
