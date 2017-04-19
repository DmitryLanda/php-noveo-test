<?php
namespace NoveoTestBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationFailedException extends \RuntimeException
{
    /**
     * @var ConstraintViolationList
     */
    private $violations;

    public function __construct($message = "", ConstraintViolationList $violations, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->violations = $violations;
    }

    public function getViolations()
    {
        return $this->violations;
    }
}
