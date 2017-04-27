<?php

namespace NoveoTestBundle\Tests\Controller;

use NoveoTestBundle\Tests\DatabaseTestCase;

/**
 * @group database
 */
class GroupsControllerTest extends DatabaseTestCase
{
    /**
     * @resetDatabase
     */
    public function testPostGroupSuccessful()
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('POST', '/groups', [], [], ['CONTENT_TYPE' => 'application/json'], '{"name": "New Group"}');

        //verify
        $this->assertStatusCode(201, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonEquals('{"id":4,"name":"New Group","users":[]}', $client->getResponse()->getContent());
        //check new user available through api
        $client->request('GET', '/groups/4');
        $this->assertStatusCode(200, $client);
    }

    /**
     * @dataProvider postGroupFailedDataProvider
     * @param string $postJson
     * @param string $expectedResponse
     */
    public function testPostGroupFailed($postJson, $expectedResponse)
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('POST', '/groups', [], [], ['CONTENT_TYPE' => 'application/json'], $postJson);

        //verify
        $this->assertStatusCode(400, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonEquals($expectedResponse, $client->getResponse()->getContent(), ['code']);
    }

    public function postGroupFailedDataProvider()
    {
        return [
            //bad json
            ['', '{"error":{"message":"Could not decode JSON, syntax error - malformed JSON.","type":"common","details":[]}}'],
            //empty request
            [
                '{}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"name"}'
                . ']}}'
            ],
            //empty name
            [
                '{"name": ""}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"name"}'
                . ']}}'
            ],
            //empty name
            [
                '{"name": null}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"name"}'
                . ']}}'
            ],
            //group with given name already exists
            [
                '{"name": "empty group"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value is already used.","code":"skipped","name":"name"}'
                . ']}}'
            ]
        ];
    }

    public function testGetGroups()
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('GET', '/groups');

        //verify
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertEquals(
            '['
                . '{"id":1,"name":"first group","users":['
                    . '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"},'
                    . '{"id":2,"email":"user-2@foo.com","enabled":true,"first_name":"Enabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
                . ']},'
                . '{"id":2,"name":"second group","users":['
                    . '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
                . ']},'
                . '{"id":3,"name":"empty group","users":[]}]',
            $client->getResponse()->getContent()
        );
    }

    public function testGetGroupNotFound()
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('GET', '/groups/404');

        //verify
        $this->assertStatusCode(404, $client);
    }

    /**
     * @dataProvider getGroupDataProvider
     * @param int $id
     * @param string $expectedResponse
     */
    public function testGetGroup($id, $expectedResponse)
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('GET', sprintf('/groups/%s', $id));

        //verify
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonEquals($expectedResponse, $client->getResponse()->getContent());
    }

    public function getGroupDataProvider()
    {
        return [
            [1, '{"id":1,"name":"first group","users":['
                    . '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"},'
                    . '{"id":2,"email":"user-2@foo.com","enabled":true,"first_name":"Enabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
                . ']}'],
            [2, '{"id":2,"name":"second group","users":['
                    . '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
                . ']}'],
            [3, '{"id":3,"name":"empty group","users":[]}'],
        ];
    }

    /**
     * @resetDatabase
     */
    public function testPatchGroupSuccessful()
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('PATCH', '/groups/3', [], [], ['CONTENT_TYPE' => 'application/json'], '{"name": "New Group"}');

        //verify
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonEquals('{"id":3,"name":"New Group","users":[]}', $client->getResponse()->getContent());
    }

    public function testPatchGroupNotFound()
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('PATCH', '/groups/404', [], [], ['CONTENT_TYPE' => 'application/json'], '{"name": "not found"}');

        //verify
        $this->assertStatusCode(404, $client);
    }

    /**
     * @dataProvider patchGroupFailedDataProvider
     * @param string $patchJson
     * @param string $expectedResponse
     */
    public function testPatchGroupFailed($id, $patchJson, $expectedResponse)
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('PATCH', sprintf('/groups/%s', $id), [], [], ['CONTENT_TYPE' => 'application/json'], $patchJson);

        //verify
        $this->assertStatusCode(400, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonEquals($expectedResponse, $client->getResponse()->getContent(), ['code']);
    }

    public function patchGroupFailedDataProvider()
    {
        return [
            //bad json
            [1, '', '{"error":{"message":"Could not decode JSON, syntax error - malformed JSON.","type":"common","details":[]}}'],
            //empty request
            [
                1,
                '{}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"name"}'
                . ']}}'
            ],
            //empty name
            [
                2,
                '{"name": ""}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"name"}'
                . ']}}'
            ],
            //empty name
            [
                3,
                '{"name": null}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"name"}'
                . ']}}'
            ],
            //group with given name already exists
            [
                1,
                '{"name": "empty group"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value is already used.","code":"skipped","name":"name"}'
                . ']}}'
            ]
        ];
    }

    /**
     * @dataProvider addUserNotFoundDataProvider
     * @param int $groupId
     * @param int $userId
     */
    public function testAddUserNotFound($groupId, $userId)
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('POST', sprintf('/groups/%s/users/%s', $groupId, $userId));

        //verify
        $this->assertStatusCode(404, $client);
    }

    public function addUserNotFoundDataProvider()
    {
        return [
            //user not found
            [1, 404],
            //group not found
            [404, 1],
            //both user and group not found
            [404, 404]
        ];
    }

    /**
     * @resetDatabase
     * @dataProvider addUserSuccessfulDataProvider
     * @param int $groupId
     * @param int $userId
     * @param string $expectedResponse
     */
    public function testAddUserSuccessful($groupId, $userId, $expectedResponse)
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('POST', sprintf('/groups/%s/users/%s', $groupId, $userId));

        //verify
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonEquals($expectedResponse, $client->getResponse()->getContent());
    }

    public function addUserSuccessfulDataProvider()
    {
        return [
            [
                1,
                3,
                '{"id":1,"name":"first group","users":['
                    . '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"},'
                    . '{"id":2,"email":"user-2@foo.com","enabled":true,"first_name":"Enabled","last_name":"User","created_at":"2017-01-01 12:00:00"},'
                    . '{"id":3,"email":"user-3@foo.com","enabled":true,"first_name":null,"last_name":"Empty First Name","created_at":"2017-01-01 12:00:00"}'
                . ']}'
            ],
            [
                2,
                1,
                '{"id":2,"name":"second group","users":['
                    . '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
                . ']}'
            ],
            [
                3,
                1,
                '{"id":3,"name":"empty group","users":['
                    . '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
                . ']}'
            ]
        ];
    }

    /**
     * @resetDatabase
     * @dataProvider removeUserSuccessfulDataProvider
     * @param int $groupId
     * @param int $userId
     * @param string $expectedResponse
     */
    public function testRemoveUserSuccessful($groupId, $userId, $expectedResponse)
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('DELETE', sprintf('/groups/%s/users/%s', $groupId, $userId));

        //verify
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonEquals($expectedResponse, $client->getResponse()->getContent());
    }

    public function removeUserSuccessfulDataProvider()
    {
        return [
            [
                1,
                2,
                '{"id":1,"name":"first group","users":['
                    . '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
                . ']}'
            ],
            [
                2,
                1,
                '{"id":2,"name":"second group","users":[]}'
            ]
        ];
    }

    /**
     * @dataProvider removeUserNotFoundDataProvider
     * @param int $groupId
     * @param int $userId
     */
    public function testRemoveUserNotFound($groupId, $userId)
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('DELETE', sprintf('/groups/%s/users/%s', $groupId, $userId));

        //verify
        $this->assertStatusCode(404, $client);
    }

    public function removeUserNotFoundDataProvider()
    {
        return [
            //user not found
            [1, 404],
            //group not found
            [404, 1],
            //both user and group not found
            [404, 404]
        ];
    }
}
