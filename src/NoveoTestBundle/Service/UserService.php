<?php

namespace NoveoTestBundle\Service;

use FOS\UserBundle\Model\UserManager;
use NoveoTestBundle\DTO\CreateUserRequest;
use NoveoTestBundle\DTO\UpdateUserRequest;
use NoveoTestBundle\Entity\User;
use NoveoTestBundle\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(UserManager $userManager, ValidatorInterface $validator)
    {

        $this->userManager = $userManager;
        $this->validator = $validator;
    }

    public function createUser(CreateUserRequest $data)
    {
        $user = $this->userManager->createUser();
        $user->setFirstName($data->firstName)
            ->setLastName($data->lastName)
            ->setEmail($data->email)
            ->setPlainPassword($data->password);

        $errors = $this->validator->validate($user);
        if ($errors->count()) {
            throw new ValidationFailedException('Validation failed', $errors);
        }

        $this->userManager->updateUser($user);


        return $user;
    }

    public function updateUser(User $user, UpdateUserRequest $data)
    {
        $user->setFirstName($data->firstName)
            ->setLastName($data->lastName)
            ->setEmail($data->email);

        $errors = $this->validator->validate($user);
        if ($errors->count()) {
            throw new ValidationFailedException('Validation failed', $errors);
        }

        $this->userManager->updateUser($user);

        return $user;
    }
}