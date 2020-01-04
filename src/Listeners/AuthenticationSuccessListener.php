<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class AuthenticationSuccessListener
{
    private $tokenTTL;

    public function __construct($tokenTTL)
    {
        $this->tokenTTL = $tokenTTL;
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $response = $event->getResponse();

        $token = $data['token'];
        unset($data['token']);
        $event->setData($data);

        $response->headers->setCookie(
            new cookie('BEARER', $token, 
                (new \DateTime())
                    ->add(new \DateInterval('PT' . $this->tokenTTL .'S'))
            )
        );
        $response->sendHeaders();
    }
}