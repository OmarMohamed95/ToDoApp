<?php

namespace App\Listeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class RefreshTokenListener implements EventSubscriberInterface
{
    private $ttl;

    public function __construct($ttl)
    {
        $this->ttl = $ttl;
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'setRefreshToken'
        ];
    }

    public function setRefreshToken(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $response = $event->getResponse();

        $refreshToken = $data['refresh_token'];

        if($refreshToken)
        {
            $response->headers->setCookie(
                new cookie('REFRESH_TOKEN', $refreshToken, 
                    (new \DateTime())
                        ->add(new \DateInterval('PT' . $this->ttl .'S'))
                )
            );
            $response->sendHeaders();
        }        
    }
}