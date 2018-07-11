<?php

namespace Osm\EasyRestBundle\EventListener;

use Osm\EasyRestBundle\Serializer\JsonSerializerFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Listener for converting controller response to JsonResponse
 *
 * @package Osm\EasyRestBundle\EventListener
 */
class JsonResponseListener
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * JsonResponseListener constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        switch ($method) {
            case Request::METHOD_POST:
                $statusCode = Response::HTTP_CREATED;
                break;
            case Request::METHOD_DELETE:
                $statusCode = Response::HTTP_NO_CONTENT;
                break;
            default:
                $statusCode = Response::HTTP_OK;
                break;
        }

        $body = $this->serializer->serialize($result, JsonSerializerFactory::FORMAT);
        $event->setResponse(JsonResponse::fromJsonString($body, $statusCode));
    }
}
