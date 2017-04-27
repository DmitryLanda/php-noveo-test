<?php
namespace NoveoTestBundle\Service;

use FOS\UserBundle\Model\GroupManager;
use NoveoTestBundle\DTO\CreateGroupRequest;
use NoveoTestBundle\DTO\UpdateGroupRequest;
use NoveoTestBundle\Entity\Group;
use NoveoTestBundle\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GroupService
{
    /**
     * @var GroupManager
     */
    private $groupManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(GroupManager $groupManager, ValidatorInterface $validator)
    {
        $this->groupManager = $groupManager;
        $this->validator = $validator;
    }

    public function createGroup(CreateGroupRequest $data)
    {
        $group = $this->groupManager->createGroup($data->name);
        $errors = $this->validator->validate($group);
        if ($errors->count()) {
            throw new ValidationFailedException('Validation failed', $errors);
        }

        $this->groupManager->updateGroup($group);

        return $group;
    }

    public function updateGroup(Group $group, UpdateGroupRequest $data)
    {
        $group->setName($data->name);
        $errors = $this->validator->validate($group);
        if ($errors->count()) {
            throw new ValidationFailedException('Validation failed', $errors);
        }

        $this->groupManager->updateGroup($group);

        return $group;
    }
}