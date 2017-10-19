<?php

namespace Osm\EasyRestBundle\Exception;


use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends UnprocessableEntityHttpException
{

    /**
     * @var ConstraintViolationListInterface
     */
    private $violationList;

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolationList()
    {
        return $this->violationList;
    }

    /**
     * @param ConstraintViolationListInterface $violationList
     */
    public function setViolationList(ConstraintViolationListInterface $violationList)
    {
        $this->violationList = $violationList;
    }

    /**
     * ValidationException constructor.
     *
     * @param ConstraintViolationListInterface $constraintViolationList
     * @param null|string                      $message
     * @param \Exception|null                  $previous
     * @param int                              $code
     */
    public function __construct(
        ConstraintViolationListInterface $constraintViolationList,
        $message = 'Validation failed',
        \Exception $previous = null,
        $code = 0
    ) {
        $this->violationList = $constraintViolationList;
        parent::__construct($message, $previous, $code);
    }

}
