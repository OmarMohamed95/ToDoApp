<?php

namespace App\Service;

use App\Entity\User;
use App\Service\FileUploader;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserService extends AbstractController
{
    private $entityManager;
    private $userRepo;
    private $fileUploader;
    private $tokenStorage;
    private $authenticationSuccessHandler;
    private $statusCode;
    private $message;
    private $user;
    private $passwordEncoder;
    private $JWTManager;
    private $username;
    private $email;
    private $password = null;
    private $image = null;
    private $role = ['ROLE_USER'];
    private $source = 0;

    public function __construct(FileUploader $fileUploader, EntityManagerInterface $entityManager, UserRepository $userRepo, UserPasswordEncoderInterface $passwordEncoder, JWTTokenManagerInterface $JWTManager, AuthenticationSuccessHandler $authenticationSuccessHandler, TokenStorageInterface $tokenStorage)
    {
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
        $this->userRepo = $userRepo;
        $this->passwordEncoder = $passwordEncoder;
        $this->JWTManager = $JWTManager;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->tokenStorage = $tokenStorage;
    }

    public function setCredentials($credentials)
    {
        $this->username = $credentials['username'];
        $this->email = $credentials['email'];
        $this->password = $credentials['password'] ?? $this->password;
        $this->image = $credentials['image'] ?? $this->image;
        $this->role = $credentials['role'] ?? $this->role;
        $this->source = $credentials['source'] ?? $this->source;
    }

    public function add()
    {
        if($this->username & $this->email)
        {
            $this->user = new User();

            $this->user->setUsername($this->username);
            $this->user->setEmail($this->email);
            $this->user->setRoles($this->role);
            $this->user->setSource($this->source);

            if($this->source === 0)
            {
                $encodedPassword = $this->passwordEncoder->encodePassword($this->user, $this->password);
                $this->user->setPassword($encodedPassword);
            }

            if($this->image)
            {
                $this->fileUploader->setDirectoryName('user');
                $fileName = $this->fileUploader->upload($this->image)->getFileName();

                $this->user->setImage($fileName);
            }

            $this->entityManager->persist($this->user);
            $this->entityManager->flush();

            $this->statusCode = 200;
            $this->message = 'Congratulations, Your account has been created successfully!';
            
            return $this->user;
        }
        else
        {
            $this->statusCode = 400;
            $this->message = 'All fields are requird!';
        }

    }

    public function checkIfUserExists($email)
    {
        $this->user = $this->userRepo->findOneBy(['email' => $email]);

        return ($this->user) ? true : false; 
    }

    public function generateAccessToken()
    {
        // this line of code is not necessary because the handleAuthenticationSuccess function make it for you if you do not send the jwt as a second parameter
        $jwt = $this->JWTManager->create($this->user);

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($this->user, $jwt);
    }

    public function response()
    {
        return ['statusCode' => $this->statusCode, 'message' => $this->message];
    }

    /**
     * @param data properties you need from the user object
     * @return user the user object  
     */
    public function getCurrentUser($data = null)
    {
        $token = $this->tokenStorage->getToken();
        if ($token) {

            $user = $token->getUser();
            
            if($data)
            {
                foreach($data as $k => $v)
                {
                    $userData[$k] = $user->$v();
                }
                return $userData;
            } 
            else
            {
                return $user;
            }

        } else {
            return ['user' => null];
        }
    }
}