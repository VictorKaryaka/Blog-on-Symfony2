<?php

namespace Blogger\BlogBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class ExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $response = new Response();

        if ($exception instanceof AuthenticationCredentialsNotFoundException) {
            $response->setContent(json_encode(['error' => 'The API key is not valid!']));
        }

        if ($exception instanceof AccessDeniedHttpException) {
            $response->setContent(json_encode(['error' => 'Access denied']));
        }

        $event->setResponse($response);
    }
}