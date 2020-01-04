<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Serializer\SerializerService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class GoogleLoginController extends AbstractFOSRestController
{
    private $clientID;
    private $clientSecret;
    private $redirectUri;
    private $client;
    private $user;
    const SOURCE = 1;

    public function __construct($clientID, $clientSecret, $redirectUri)
    {
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;

        $this->googleClient();
    }

    public function googleClient()
    {
        $this->client = new \Google_Client();
        $this->client->setClientId($this->clientID);
        $this->client->setClientSecret($this->clientSecret);
        $this->client->setRedirectUri($this->redirectUri);
        $this->client->addScope("email");
        $this->client->addScope("profile");
    }

    /**
     * @Route("/login/google", name="google_login", methods={"GET"})
     */
    public function googleLoginTemplate()
    {
        $view = $this->redirectView($this->client->createAuthUrl(), 301);
        return $this->handleView($view);
    }

    /**
     * @Route("api/login/google/callback", name="google_login_callback", methods={"GET"})
     */
    public function googleLoginCallback(UserService $userService)
    {
        if (isset($_GET['code'])) {
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            $this->client->setAccessToken($token['access_token']);
            
            $google_oauth = new \Google_Service_Oauth2($this->client);
            $google_account_info = $google_oauth->userinfo->get();
            $this->user['email'] =  $google_account_info->email;
            $this->user['username'] =  $google_account_info->name;
            $this->user['source'] =  self::SOURCE;

            $user = $userService->checkIfUserExists($this->user['email']);

            if(!$user)
            {
                $this->addUser($userService);
            }

            $accessToken = $userService->generateAccessToken();

            $view = $this->routeRedirectView('home_index', [], 301);
            return $this->handleView($view);
          } 
    }

    public function addUser(UserService $userService)
    {
        $userService->setCredentials($this->user);
        $userService->add();
    }
    
}
