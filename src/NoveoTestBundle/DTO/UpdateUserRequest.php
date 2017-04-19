<?php

namespace NoveoTestBundle\DTO;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @JMS\ExclusionPolicy("all")
 */
class UpdateUserRequest
{
    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @Assert\Type("string")
     */
    public $firstName;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @Assert\Type("string")
     */
    public $lastName;

    /**
     * @var boolean
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @Assert\Type("boolean")
     */
    public $enabled;
}