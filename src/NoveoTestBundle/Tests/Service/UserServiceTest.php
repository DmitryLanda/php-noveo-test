<?php
namespace NoveoTestBundle\Tests\Service;

use NoveoTestBundle\DTO\CreateUserRequest;
use NoveoTestBundle\DTO\UpdateUserRequest;
use NoveoTestBundle\Service\UserService;
use PHPUnit\Framework\TestCase;

/**
 * @group mock
 */
class UserServiceTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $managerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $validatorMock;

    /**
     * @var UserService
     */
    private $userService;

    public function testCreateUser()
    {
        $userMock = $this->getMockBuilder('NoveoTestBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();
        $userMock->expects($this->once())
            ->method('setFirstName')
            ->willReturnSelf();
        $userMock->expects($this->once())
            ->method('setLastName')
            ->willReturnSelf();
        $userMock->expects($this->once())
            ->method('setEmail')
            ->willReturnSelf();
        $userMock->expects($this->once())
            ->method('setPlainPassword')
            ->willReturnSelf();

        $violationsMock = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolationListInterface')
            ->getMock();
        $violationsMock->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn($violationsMock);

        $this->managerMock->expects($this->once())
            ->method('createUser')
            ->willReturn($userMock);
        $this->managerMock->expects($this->once())
            ->method('updateUser')
            ->willReturn($userMock);

        $this->userService->createUser(new CreateUserRequest());
    }

    /**
     * @expectedException \NoveoTestBundle\Exception\ValidationFailedException
     */
    public function testCreateUserFailure()
    {
        $userMock = $this->getMockBuilder('NoveoTestBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();
        $userMock->expects($this->once())
            ->method('setFirstName')
            ->willReturnSelf();
        $userMock->expects($this->once())
            ->method('setLastName')
            ->willReturnSelf();
        $userMock->expects($this->once())
            ->method('setEmail')
            ->willReturnSelf();
        $userMock->expects($this->once())
            ->method('setPlainPassword')
            ->willReturnSelf();

        $violationsMock = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolationListInterface')
            ->getMock();
        $violationsMock->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn($violationsMock);

        $this->managerMock->expects($this->once())
            ->method('createUser')
            ->willReturn($userMock);
        $this->managerMock->expects($this->never())
            ->method('updateUser')
            ->willReturn($userMock);

        $this->userService->createUser(new CreateUserRequest());
    }

    public function testUpdateUserEmptyData()
    {
        $userMock = $this->getMockBuilder('NoveoTestBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();
        $userMock->expects($this->never())
            ->method('setFirstName')
            ->willReturnSelf();
        $userMock->expects($this->never())
            ->method('setLastName')
            ->willReturnSelf();
        $userMock->expects($this->never())
            ->method('setEmail')
            ->willReturnSelf();
        $userMock->expects($this->never())
            ->method('setPlainPassword')
            ->willReturnSelf();

        $violationsMock = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolationListInterface')
            ->getMock();
        $violationsMock->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn($violationsMock);

        $this->managerMock->expects($this->once())
            ->method('updateUser')
            ->willReturn($userMock);

        $this->userService->updateUser($userMock, new UpdateUserRequest());
    }

    public function testUpdateUserWithData()
    {
        $userMock = $this->getMockBuilder('NoveoTestBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();
        $userMock->expects($this->once())
            ->method('setFirstName')
            ->willReturnSelf();
        $userMock->expects($this->never())
            ->method('setLastName')
            ->willReturnSelf();
        $userMock->expects($this->once())
            ->method('setEmail')
            ->willReturnSelf();
        $userMock->expects($this->never())
            ->method('setPlainPassword')
            ->willReturnSelf();

        $violationsMock = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolationListInterface')
            ->getMock();
        $violationsMock->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn($violationsMock);

        $this->managerMock->expects($this->once())
            ->method('updateUser')
            ->willReturn($userMock);

        $data = new UpdateUserRequest();
        $data->firstName = 'foo';
        $data->email = 'test@email.com';

        $this->userService->updateUser($userMock, $data);
    }

    /**
     * @expectedException \NoveoTestBundle\Exception\ValidationFailedException
     */
    public function testUpdateUserFailure()
    {
        $userMock = $this->getMockBuilder('NoveoTestBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();
        $userMock->expects($this->once())
            ->method('setFirstName')
            ->willReturnSelf();
        $userMock->expects($this->once())
            ->method('setLastName')
            ->willReturnSelf();
        $userMock->expects($this->once())
            ->method('setEmail')
            ->willReturnSelf();
        $userMock->expects($this->once())
            ->method('setPlainPassword')
            ->willReturnSelf();

        $violationsMock = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolationListInterface')
            ->getMock();
        $violationsMock->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn($violationsMock);

        $this->managerMock->expects($this->never())
            ->method('updateUser')
            ->willReturn($userMock);

        $data = new UpdateUserRequest();
        $data->firstName = 'foo';
        $data->lastName = 'bar';
        $data->email = 'test@email.com';
        $data->password = '123';

        $this->userService->updateUser($userMock, $data);
    }

    public function setUp()
    {
        $this->managerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->validatorMock = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->getMock();
        $this->userService = new UserService($this->managerMock, $this->validatorMock);

        parent::setUp();
    }
}