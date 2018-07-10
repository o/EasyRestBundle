<?php

namespace Osm\EasyRestBundle\Utility;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ExceptionWrapper implements \JsonSerializable
{
    /**
     * @var int
     */
    protected $code = 0;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * @var array
     */
    protected $trace = [];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param $errors
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param $trace
     * @return $this
     */
    public function setTrace($trace)
    {
        $this->trace = $trace;

        return $this;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Helper method for creating list of validation errors from ConstraintViolationList
     *
     * @param ConstraintViolationListInterface $errors
     * @return $this
     */
    public function setErrorsFromConstraintViolations(ConstraintViolationListInterface $errors)
    {
        $this->errors = [];
        foreach ($errors as $error) {
            $this->addError(
                $error->getPropertyPath(),
                $error->getMessage()
            );
        }

        return $this;
    }

    /**
     * Adds an error
     *
     * @param $path
     * @param $message
     * @return $this
     */
    public function addError($path, $message)
    {
        array_push(
            $this->errors,
            [
                'path' => $path,
                'message' => $message,
            ]
        );

        return $this;
    }

    /**
     * Returns translated name of status code
     *
     * @return string
     */
    private function getStatusTextFromCode()
    {
        if (isset(Response::$statusTexts[$this->statusCode])) {
            return Response::$statusTexts[$this->statusCode];
        }

        return '';
    }

    /**
     * Creates a response
     *
     * @return JsonResponse
     */
    public function getResponse()
    {
        return new JsonResponse(
            $this->jsonSerialize(),
            $this->statusCode,
            $this->headers
        );
    }

    /**
     * Returns an array to represent error
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'status_code' => $this->statusCode,
            'status_text' => $this->getStatusTextFromCode(),
            'code' => $this->code,
            'message' => $this->message,
            'errors' => $this->errors,
            'trace' => $this->trace,
        ];
    }

}
