<?php

namespace Osm\EasyRestBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener as HttpKernelExceptionListener;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionListener extends HttpKernelExceptionListener
{

    /**
     * Overrides standard functionality and passes actual exception to ExceptionController
     *
     * @param \Exception $exception
     * @param Request    $request
     * @return Request|static
     */
    protected function duplicateRequest(\Exception $exception, Request $request)
    {
        $attributes = array(
            '_controller' => $this->controller,
            'exception' => $exception,
            'logger' => $this->logger instanceof DebugLoggerInterface ? $this->logger : null,
        );
        $request = $request->duplicate(null, null, $attributes);
        $request->setMethod('GET');

        return $request;
    }


}
