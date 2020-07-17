<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\UserService;
use App\Util\CacheInterface;

/**
 * Auth controller
 *
 * @Route("/api/")
 */
class AuthController extends BaseController
{
    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->response = new Response;
    }

    /**
     * Check if the user is authenticated
     *
     * @Route("auth/check", name="check_auth", methods={"GET"})
     *
     * @return Response
     */
    public function checkIfAuthenticated()
    {
        $refreshToken = $this->request->cookies->get('REFRESH_TOKEN');

        if ($refreshToken) {
            $response = ['isAuthenticated' => true];
        } else {
            $response = ['isAuthenticated' => false];
        }
        
        return $this->baseView($response);
    }

    /**
     * Get current user data
     *
     * @Route("auth/user", name="user", methods={"GET"})
     *
     * @param UserService $user
     *
     * @return Response
     */
    public function getCurrentUser(UserService $user)
    {
        $currentUser = $user->getCurrentUser(['id' => 'getId', 'username' => 'getUsername']);

        if ($currentUser === null) {
            return $this->baseView(null, 204);
        }

        return $this->baseView($currentUser);
    }

    /**
     * Logout action
     *
     * @Route("logout", name="logout", methods={"GET"})
     *
     * @param CacheInterface $redisCache
     *
     * @return Response
     */
    public function logout(CacheInterface $redisCache)
    {
        $redisCache->invalidateCache(['all-cached-values']);

        $this->response->headers->clearCookie('BEARER');
        $this->response->headers->clearCookie('REFRESH_TOKEN');
        $this->response->send();

        // $refreshToken = $this->request->cookies->get('REFRESH_TOKEN');
        
        return $this->baseView(['message' => 'logged out successfully']);
    }
}
