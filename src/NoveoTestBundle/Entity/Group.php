<?php

namespace NoveoTestBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_groups")
 * @UniqueEntity("name")
 *
 * @JMS\ExclusionPolicy("all")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     * @JMS\Expose
     * @JMS\Groups({"Registration"})
     */
    private $users;

    public function __construct($name, $roles = array())
    {
        parent::__construct($name, $roles = array());
        $this->users = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User[] $users
     * @return Group
     */
    public function setUsers(array $users)
    {
       $this->users->clear();

       foreach ($users as $user) {
           $this->addUser($user);
       }

       return $this;
    }

    public function addUser(User $user)
    {
        if (!$this->hasUser($user)) {
            $this->users->add($user);
            $user->addGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user)
    {
        if ($this->hasUser($user)) {
            $this->users->removeElement($user);
            $user->removeGroup($this);
        }

        return $this;
    }

    public function hasUser(User $user)
    {
        return $this->users->contains($user);
    }
}