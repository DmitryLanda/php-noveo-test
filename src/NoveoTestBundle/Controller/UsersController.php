<?php

namespace NoveoTestBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use NoveoTestBundle\DTO\CreateUserRequest;
use NoveoTestBundle\DTO\UpdateUserRequest;
use NoveoTestBundle\Entity\User;
use NoveoTestBundle\Exception\ValidationFailedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UsersController extends FOSRestController
{
    /**
     * @Rest\View(statusCode=201, serializerGroups={"Registration"})
     * @Rest\Post("/users")
     * @ParamConverter("data", converter="fos_rest.request_body")
     */
    public function postUserAction(CreateUserRequest $data, ConstraintViolationListInterface $validationErrors)
    {
        if ($validationErrors->count()) {
            throw new ValidationFailedException('Validation failed', $validationErrors);
        }

        return $this->get('noveo_test.service.user')->createUser($data);
    }

    /**
     * @Rest\View()
     */
    public function getUsersAction()
    {
        return $this->getUserManager()->findUsers();
    }

    /**
     * @Rest\View()
     */
    public function getUserAction(User $user)
    {
        return $user;
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/users/{user}")
     * @ParamConverter("data", converter="fos_rest.request_body")
     */
    public function patchUserAction(User $user, UpdateUserRequest $data, ConstraintViolationListInterface $validationErrors)
    {
        if ($validationErrors->count()) {
            throw new ValidationFailedException('Validation failed', $validationErrors);
        }

        return $this->get('noveo_test.service.user')->updateUser($user, $data);
    }


    /**
     * @return \FOS\UserBundle\Doctrine\UserManager
     */
    private function getUserManager()
    {
        return $this->get('fos_user.user_manager');
    }
}
