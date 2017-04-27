<?php
namespace NoveoTestBundle\Tests\Service;

use NoveoTestBundle\DTO\CreateGroupRequest;
use NoveoTestBundle\DTO\UpdateGroupRequest;
use NoveoTestBundle\Service\GroupService;
use PHPUnit\Framework\TestCase;

/**
 * @group mock
 */
class GroupServiceTest extends TestCase
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
     * @var GroupService
     */
    private $groupService;

    public function testCreateGroup()
    {
        $groupMock = $this->getMockBuilder('NoveoTestBundle\Entity\Group')
            ->disableOriginalConstructor()
            ->getMock();

        $violationsMock = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolationListInterface')
            ->getMock();
        $violationsMock->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn($violationsMock);

        $this->managerMock->expects($this->once())
            ->method('createGroup')
            ->willReturn($groupMock);
        $this->managerMock->expects($this->once())
            ->method('updateGroup')
            ->willReturn($groupMock);

        $this->groupService->createGroup(new CreateGroupRequest());
    }

    /**
     * @expectedException \NoveoTestBundle\Exception\ValidationFailedException
     */
    public function testCreateGroupFailure()
    {
        $groupMock = $this->getMockBuilder('NoveoTestBundle\Entity\Group')
            ->disableOriginalConstructor()
            ->getMock();

        $violationsMock = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolationListInterface')
            ->getMock();
        $violationsMock->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn($violationsMock);

        $this->managerMock->expects($this->once())
            ->method('createGroup')
            ->willReturn($groupMock);
        $this->managerMock->expects($this->never())
            ->method('updateGroup')
            ->willReturn($groupMock);

        $this->groupService->createGroup(new CreateGroupRequest());
    }

    public function testUpdateGroup()
    {
        $groupMock = $this->getMockBuilder('NoveoTestBundle\Entity\Group')
            ->disableOriginalConstructor()
            ->getMock();
        $groupMock->expects($this->once())
            ->method('setName')
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
            ->method('updateGroup')
            ->willReturn($groupMock);

        $this->groupService->updateGroup($groupMock, new UpdateGroupRequest());
    }

    /**
     * @expectedException \NoveoTestBundle\Exception\ValidationFailedException
     */
    public function testUpdateUserFailure()
    {
        $groupMock = $this->getMockBuilder('NoveoTestBundle\Entity\Group')
            ->disableOriginalConstructor()
            ->getMock();
        $groupMock->expects($this->once())
            ->method('setName')
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
            ->method('updateGroup')
            ->willReturn($groupMock);

        $this->groupService->updateGroup($groupMock, new UpdateGroupRequest());
    }

    public function setUp()
    {
        $this->managerMock = $this->getMockBuilder('FOS\UserBundle\Model\GroupManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->validatorMock = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->getMock();
        $this->groupService = new GroupService($this->managerMock, $this->validatorMock);

        parent::setUp();
    }
}