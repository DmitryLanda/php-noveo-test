<?php

namespace NoveoTestBundle\DTO;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @JMS\ExclusionPolicy("all")
 */
class UpdateGroupRequest
{
    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @Assert\NotBlank()
     */
    public $name;

}