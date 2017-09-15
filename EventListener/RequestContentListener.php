<?php

namespace Osm\EasyRestBundle\EventListener;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestContentListener
{

    /**
     * @param GetResponseEvent $event
     * @return bool
     * @throws BadRequestHttpException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();

        if (count($request->request->all())) {
            return false;
        }

        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return false;
        }

        $content = $request->getContent();

        if (empty($content)) {
            return false;
        }

        $data = json_decode($content, true);
        if (is_array($data)) {
            $request->request = new ParameterBag($data);
        } else {
            throw new BadRequestHttpException('Unexpected JSON request');
        }

        return true;
    }
}
