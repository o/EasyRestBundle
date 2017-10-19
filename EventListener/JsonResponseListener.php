<?php

namespace Osm\EasyRestBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Listener for converting controller response to JsonResponse
 *
 * @package Osm\EasyRestBundle\EventListener
 */
class JsonResponseListener
{

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        switch ($method) {
            case 'POST':
                $statusCode = 201;
                break;
            case 'DELETE':
                $statusCode = 204;
                break;
            default:
                $statusCode = 200;
                break;
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setStatusCode($statusCode);
        $jsonResponse->setData($result);

        $event->setResponse($jsonResponse);
    }
}
