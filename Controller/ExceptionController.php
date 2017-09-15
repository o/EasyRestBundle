<?php

namespace Osm\EasyRestBundle\Controller;

use Osm\EasyRestBundle\Utility\ExceptionWrapper;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController extends BaseExceptionController
{

    /**
     * Overrides standard Twig exception controller
     *
     * @param Request                   $request
     * @param FlattenException          $exception
     * @param DebugLoggerInterface|null $logger
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));

        $wrapper = new ExceptionWrapper();
        return $wrapper->setCode($exception->getCode())
            ->setMessage($exception->getMessage())
            ->setStatusCode($exception->getStatusCode())
            ->setTrace($this->debug ? $exception->getTrace() : [])
            ->getResponse();
    }
}
