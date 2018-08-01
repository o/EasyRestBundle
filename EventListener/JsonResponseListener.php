<?php

namespace Osm\EasyRestBundle\EventListener;

use Osm\EasyRestBundle\Serializer\RestJsonSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Listener for converting controller response to JsonResponse
 *
 * @package Osm\EasyRestBundle\EventListener
 */
class JsonResponseListener
{

    /**
     * @var RestJsonSerializer
     */
    private $serializer;

    /**
     * JsonResponseListener constructor.
     * @param RestJsonSerializer $serializer
     */
    public function __construct(RestJsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     * @throws \Symfony\Component\Serializer\Exception\NotEncodableValueException
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

        $body = $this->serializer->serialize($result);

        $event->setResponse(JsonResponse::fromJsonString($body, $statusCode));
    }
}
