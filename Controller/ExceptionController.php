<?php

namespace Osm\EasyRestBundle\Controller;


use Osm\EasyRestBundle\Utility\ExceptionWrapper;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseExceptionController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController extends BaseExceptionController
{
    public function __construct(\Twig_Environment $twig, $debug)
    {
        parent::__construct($twig, $debug);
    }

    /**
     * Converts an Exception to a Response.
     *
     * @param Request $request The request
     * @param FlattenException $exception A FlattenException instance
     * @param DebugLoggerInterface $logger A DebugLoggerInterface instance
     *
     * @param string $_format
     * @return JsonResponse
     */
    public function showAction(
        Request $request,
        FlattenException $exception,
        DebugLoggerInterface $logger = null,
        $_format = 'html'
    )
    {
        $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));

        $wrapper = new ExceptionWrapper();
        $wrapper->setCode($exception->getCode());
        $wrapper->setMessage($exception->getMessage());
        $wrapper->setStatusCode($exception->getStatusCode());
        $wrapper->setTrace($this->debug ? $exception->getTrace() : array());

        return $wrapper->getResponse();
    }

}
