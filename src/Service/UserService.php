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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\ValidationService;

/**
 * UserService
 */
class UserService extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserRepository */
    private $userRepo;

    /** @var FileUploader */
    private $fileUploader;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var AuthenticationSuccessHandler */
    private $authenticationSuccessHandler;

    /** @var int */
    private $statusCode;

    /** @var mixed */
    private $message;

    /** @var User */
    private $user;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var JWTTokenManagerInterface */
    private $JWTManager;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationService */
    private $validationService;

    /** @var string */
    private $username;

    /** @var string */
    private $email;

    /** @var string */
    private $password = null;

    /** @var string */
    private $image = null;

    /** @var array */
    private $role = ['ROLE_USER'];

    /** @var int */
    private $source = 0;

    public function __construct(
        FileUploader $fileUploader,
        EntityManagerInterface $entityManager,
        UserRepository $userRepo,
        UserPasswordEncoderInterface $passwordEncoder,
        JWTTokenManagerInterface $JWTManager,
        AuthenticationSuccessHandler $authenticationSuccessHandler,
        TokenStorageInterface $tokenStorage,
        ValidatorInterface $validator,
        ValidationService $validationService
    ) {
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
        $this->userRepo = $userRepo;
        $this->passwordEncoder = $passwordEncoder;
        $this->JWTManager = $JWTManager;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->tokenStorage = $tokenStorage;
        $this->validator = $validator;
        $this->validationService = $validationService;
    }

    /**
     * Set credentials
     *
     * @param array $credentials
     *
     * @return void
     */
    public function setCredentials(array $credentials): void
    {
        $this->username = trim($credentials['username']);
        $this->email = trim($credentials['email']);
        $this->password = $credentials['password'] ?? $this->password;
        $this->image = $credentials['image'] ?? $this->image;
        $this->role = $credentials['role'] ?? $this->role;
        $this->source = $credentials['source'] ?? $this->source;
    }

    /**
     * Add user to DB
     *
     * @return void
     */
    public function add(): void
    {
        $this->user = new User();

        $this->user->setUsername($this->username);
        $this->user->setEmail($this->email);
        $this->user->setRoles($this->role);
        $this->user->setSource($this->source);

        if ($this->source === 0) {
            if (!empty($this->password)) {
                $encodedPassword = $this->passwordEncoder->encodePassword($this->user, $this->password);
                $this->user->setPassword($encodedPassword);
            } else {
                $this->user->setPassword($this->password);
            }

            if ($this->image) {
                $this->user->setImage($this->image);
            }

            $errors = $this->validator->validate($this->user);

            if (count($errors) > 0) {
                $violations = $this->validationService->identifyErrors($errors);
    
                $this->statusCode = 400;
                $this->message = $violations;
                return;
            } else {
                if ($this->image) {
                    $this->fileUploader->setDirectoryName('user');
                    $this->fileUploader->upload($this->image);
                    $this->user->setImage($this->fileUploader->getFileName());
                }
            }
        }
        
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        $this->statusCode = 200;
        $this->message = 'Congratulations, Your account has been created successfully!';
    }

    /**
     * Check if user exists in DB
     *
     * @param string $email
     *
     * @return bool
     */
    public function checkIfUserExists(string $email): bool
    {
        $this->user = $this->userRepo->findOneBy(['email' => $email]);

        return ($this->user) ? true : false;
    }

    /**
     * Generate access token
     *
     * @return JWTAuthenticationSuccessResponse
     */
    public function generateAccessToken()
    {
        // this line of code is not necessary because the handleAuthenticationSuccess function make it for you if you do not send the jwt as a second parameter
        $jwt = $this->JWTManager->create($this->user);

        return $this
            ->authenticationSuccessHandler
            ->handleAuthenticationSuccess($this->user, $jwt);
    }

    /**
     * Create response
     *
     * @return array
     */
    public function response(): array
    {
        return ['statusCode' => $this->statusCode, 'message' => $this->message];
    }

    /**
     * Get current user data
     *
     * @param array $data
     *
     * @return array|User
     */
    public function getCurrentUser(array $data = null)
    {
        $token = $this->tokenStorage->getToken();
        if ($token) {
            $user = $token->getUser();
            
            if ($data) {
                foreach ($data as $k => $v) {
                    $userData[$k] = $user->$v();
                }
                return $userData;
            } else {
                return $user;
            }
        } else {
            return ['user' => null];
        }
    }
}
