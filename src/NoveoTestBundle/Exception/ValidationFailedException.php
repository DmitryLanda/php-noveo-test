<?php
namespace NoveoTestBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationFailedException extends \RuntimeException
{
    /**
     * @var ConstraintViolationListinterface
     */
    private $violations;

    public function __construct($message = "", ConstraintViolationListinterface $violations, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->violations = $violations;
    }

    public function getViolations()
    {
        return $this->violations;
    }
}
