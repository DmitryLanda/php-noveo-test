<?php

namespace NoveoTestBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use NoveoTestBundle\Entity\User;

//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    public function testFullResponseForGetUsersAction()
    {
        //create client
        $client = $this->makeClient(false, ['HTTP_HOST' => 'noveo-test']);

        //mock real calls
        $userManager = $this->getServiceMockBuilder('fos_user.user_manager')->getMock();
        $user = $this->createUser();
        $userManager->expects($this->once())->method('findUsers')->willReturn([$user]);
        $client->getContainer()->set('fos_user.user_manager', $userManager);

        //call
        $client->request('GET', '/users');

        //verify
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonStringEqualsJsonString(
            '[{"id":null,"email":"test@email.com","enabled":true,"first_name":"Test","last_name":"User","created_at":"2017-01-01 12:00:00"}]',
                $client->getResponse()->getContent()
        );
    }

    /**
     * @dataProvider usersDataProvider
     */
    public function testGetUsers($users, $expectedCount)
    {
        //create client
        $client = $this->makeClient(false, ['HTTP_HOST' => 'noveo-test']);

        //mock real calls
        $userManager = $this->getServiceMockBuilder('fos_user.user_manager')->getMock();
        $userManager->expects($this->once())->method('findUsers')->willReturn($users);
        $client->getContainer()->set('fos_user.user_manager', $userManager);

        //call
        $client->request('GET', '/users');

        //verify
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertCount($expectedCount, $response);
    }

    public function usersDataProvider()
    {
        return [
            [[], 0], //no users found
            [[$this->createUser('1@email.com', 'First')], 1], //one user found
            [[
                $this->createUser('1@email.com', 'First'),
                $this->createUser('2@email.com', 'Second'),
                $this->createUser('3@email.com', 'Third'),
                $this->createUser('4@email.com', 'Fourth')
            ], 4] // 4 users found
        ];
    }

    private function createUser($email = 'test@email.com', $firstName = 'Test', $lastName = 'User', $createdAt = '2017-01-01 12:00:00', $enabled = true)
    {
        $user = new User();
        $user->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEnabled($enabled)
            ->setCreatedAt(new \DateTime($createdAt))
            ->setPlainPassword('password');

        return $user;
    }
}
