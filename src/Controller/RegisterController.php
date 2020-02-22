<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\UserService;

class RegisterController extends BaseController
{
    /**
     * @RequestParam(name="username", nullable=false)
     * @RequestParam(name="email", nullable=false)
     * @RequestParam(name="password", nullable=false)
     * 
     * @FileParam(name="image", default=NULL, nullable=true, strict=false)
     * 
     * @Route("/api/register", name="register", methods={"POST"})
     */
    public function register(ParamFetcher $paramFetcher, UserService $userService)
    {
        $credentials['username'] = $paramFetcher->get('username');
        $credentials['email'] = $paramFetcher->get('email');
        $credentials['password'] = $paramFetcher->get('password');
        $credentials['image'] = $paramFetcher->get('image');

        $userService->setCredentials($credentials);
        $userService->add();
        $response = $userService->response();

        if($response['statusCode'] === 200){
            $userService->generateAccessToken();
        }

        return $this->baseView($response['message'], $response['statusCode']);
    }
}
