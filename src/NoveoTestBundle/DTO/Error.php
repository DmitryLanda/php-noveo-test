<?php
namespace NoveoTestBundle\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class Error
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
    public $type;

    /**
     * @var array
     * @JMS\Expose
     */
    public $details = [];

    public function __construct($message = "Unexpected error", $type = 'common', $code = null)
    {
        $this->message = $message;
        $this->type = $type;
        $this->code = $code;
    }

    public function addDetailedError($error)
    {
        $this->details[] = $error;

        return $this;
    }
}