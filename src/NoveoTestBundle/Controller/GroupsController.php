<?php

namespace NoveoTestBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use NoveoTestBundle\DTO\CreateGroupRequest;
use NoveoTestBundle\DTO\UpdateGroupRequest;
use NoveoTestBundle\Entity\Group;
use NoveoTestBundle\Entity\User;
use NoveoTestBundle\Exception\ValidationFailedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class GroupsController extends FOSRestController
{
    /**
     * @Rest\View(statusCode=201, serializerGroups={"Registration"})
     * @Rest\Post("/groups")
     * @ParamConverter("data", converter="fos_rest.request_body")
     */
    public function postGroupAction(CreateGroupRequest $data, ConstraintViolationListInterface $validationErrors)
    {
        if ($validationErrors->count()) {
            throw new ValidationFailedException('Validation failed', $validationErrors);
        }

        return $this->get('noveo_test.service.group')->createGroup($data);
    }

    /**
     * @Rest\View()
     */
    public function getGroupsAction()
    {
        return $this->getGroupManager()->findGroups();
    }

    /**
     * @Rest\View()
     */
    public function getGroupAction(Group $group)
    {
        return $group;
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/groups/{group}")
     * @ParamConverter("data", converter="fos_rest.request_body")
     */
    public function patchGroupAction(Group $group, UpdateGroupRequest $data, ConstraintViolationListInterface $validationErrors)
    {
        if ($validationErrors->count()) {
            throw new ValidationFailedException('Validation failed', $validationErrors);
        }

        return $this->get('noveo_test.service.group')->updateGroup($group, $data);
    }

    /**
     * @Rest\View()
     */
    public function postGroupUsersAction(Group $group, User $user)
    {
        $group->addUser($user);
        $this->getGroupManager()->updateGroup($group);

        return $group;
    }

    /**
     * @Rest\View()
     */
    public function deleteGroupUsersAction(Group $group, User $user)
    {
        $group->removeUser($user);
        $this->getGroupManager()->updateGroup($group);

        return $group;
    }

    /**
     * @return \FOS\UserBundle\Doctrine\GroupManager
     */
    private function getGroupManager()
    {
        return $this->get('fos_user.group_manager');
    }
}
