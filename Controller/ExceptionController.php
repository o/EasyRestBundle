<?php

namespace Osm\EasyRestBundle\Controller;

use Osm\EasyRestBundle\Exception\ValidationException;
use Osm\EasyRestBundle\Utility\ExceptionWrapper;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ExceptionController
{

    private $debug;

    /**
     * ExceptionController constructor.
     *
     * @param $debug
     */
    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     *
     *
     * @param Request                   $request
     * @param \Exception|\Throwable     $exception
     * @param DebugLoggerInterface|null $logger
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null)
    {
        $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));

        $message = $exception->getMessage();
        $headers = [];

        if ($exception instanceof AccessDeniedException) {
            $message = 'You do not have the necessary permissions';
            $statusCode = Response::HTTP_FORBIDDEN;
        } elseif ($exception instanceof AuthenticationException) {
            $message = 'You are not authenticated';
            $statusCode = Response::HTTP_UNAUTHORIZED;
        } elseif ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $headers = $exception->getHeaders();
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $wrapper = new ExceptionWrapper();
        $wrapper->setCode($exception->getCode());
        $wrapper->setMessage($message);
        $wrapper->setStatusCode($statusCode);
        $wrapper->setHeaders($headers);

        if ($exception instanceof ValidationException) {
            $wrapper->setErrorsFromConstraintViolations($exception->getViolationList());
        }

        if ($this->debug) {
            $context = FlattenException::create($exception);
            $wrapper->setTrace($context->getTrace());
        }

        return $wrapper->getResponse();
    }

    /**
     * Gets and cleans any content that was already outputted.
     *
     * This code comes from Symfony and should be synchronized on a regular basis
     * see src/Symfony/Bundle/TwigBundle/Controller/ExceptionController.php
     *
     * @param $startObLevel
     * @return string
     */
    protected function getAndCleanOutputBuffering($startObLevel)
    {
        if (ob_get_level() <= $startObLevel) {
            return '';
        }

        Response::closeOutputBuffers($startObLevel + 1, true);

        return ob_get_clean();
    }
}
