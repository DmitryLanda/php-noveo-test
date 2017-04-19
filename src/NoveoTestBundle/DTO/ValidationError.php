<?php
namespace NoveoTestBundle\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class ValidationError
{
    /**
     * @var string
     * @JMS\Expose
     */
    public $message;

    /**
     * @var string
     * @JMS\Expose
     */
    public $code;

    /**
     * @var string
     * @JMS\Expose
     */
    public $name;

    public function __construct($message, $name, $code = null)
    {
        $this->message = $message;
        $this->name = $name;
        $this->code = $code;
    }
}