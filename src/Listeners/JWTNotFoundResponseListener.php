<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class JWTNotFoundResponseListener
{
    private $requestStack;
    private $dispatcher;

    public function __construct(RequestStack $requestStack, EventDispatcherInterface $dispatcher)
    {
        $this->requestStack = $requestStack;
        $this->dispatcher = $dispatcher;
    }

    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $refreshToken = $this->requestStack->getCurrentRequest()->cookies->get('REFRESH_TOKEN');

        if ($refreshToken) {
            // TODO: dispatch the expired JWT event ??
            // TODO: this needs to be put inside the expired listener and not here
            //$expiredEvent = new JWTExpiredEvent($event->getException(), $event->getResponse());
            $data = [
                'status'  => Response::HTTP_UNAUTHORIZED . ' Unauthorized',
                'message' => 'Expired JWT token',
            ];
            $response = new JsonResponse($data, 401);
            return $event->setResponse($response);
            //$this->dispatcher->dispatch('lexik_jwt_authentication.on_jwt_expired', $expiredEvent);
            //return false;
        }

        $data = [
            'status'  => Response::HTTP_FORBIDDEN . ' Forbidden',
            'message' => 'Missing JWT token',
        ];

        $response = new JsonResponse($data, 401);
        return $event->setResponse($response);
    }
}