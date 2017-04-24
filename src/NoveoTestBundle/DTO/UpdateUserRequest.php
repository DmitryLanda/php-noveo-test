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

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @Assert\Type("string")
     * @Assert\Length(min=5, minMessage="Password should be at least 5 characters long")
     * @Assert\Regex(pattern="/[a-z]{1,}/", message="Password should contain at least one letter in lower case")
     * @Assert\Regex(pattern="/[A-Z]{1,}/", message="Password should contain at least one letter in upper case")
     * @Assert\Regex(pattern="/[0-9]{1,}/", message="Password should contain at least one number")
     */
    public $password;

}