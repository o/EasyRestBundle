<?php

namespace Osm\EasyRestBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Listener for mapping json request body to Request object
 *
 * @package Osm\EasyRestBundle\EventListener
 */
class RequestBodyListener
{

    /**
     * @param GetResponseEvent $event
     * @return bool
     * @throws BadRequestHttpException
     * @throws \LogicException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();

        if (\count($request->request->all())) {
            return false;
        }

        if (!\in_array(
            $method,
            [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH, Request::METHOD_DELETE],
            true
        )) {
            return false;
        }

        $content = $request->getContent();

        if (empty($content)) {
            return false;
        }

        $data = json_decode($content, true);
        if (\is_array($data)) {
            $request->request->replace($data);
        } else {
            throw new BadRequestHttpException('Unexpected JSON request');
        }

        return true;
    }
}
