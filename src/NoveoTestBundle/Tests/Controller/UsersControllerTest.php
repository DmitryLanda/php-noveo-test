<?php

namespace NoveoTestBundle\Tests\Controller;

use NoveoTestBundle\Tests\DatabaseTestCase;

/**
 * @group database
 */
class UsersControllerTest extends DatabaseTestCase
{
    public function testGetUser()
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('GET', '/users/1');

        //verify
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));

        $this->assertEquals(
            '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"}',
            $client->getResponse()->getContent()
        );
    }

    public function testGetUserWithNotExistingUser()
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('GET', '/users/404');

        //verify
        $this->assertStatusCode(404, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
    }

    public function testGetUsers()
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('GET', '/users');

        //verify
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));

        $this->assertEquals(
            '['
                . '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"},'
                . '{"id":2,"email":"user-2@foo.com","enabled":true,"first_name":"Enabled","last_name":"User","created_at":"2017-01-01 12:00:00"},'
                . '{"id":3,"email":"user-3@foo.com","enabled":true,"first_name":null,"last_name":"Empty First Name","created_at":"2017-01-01 12:00:00"}'
            . ']',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @resetDatabase
     * @dataProvider postSuccessfulUserDataProvider
     * @param string $postJson
     * @param string $expectedResponse
     */
    public function testPostUserSuccessful($postJson, $expectedResponse)
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('POST', '/users', [], [], ['CONTENT_TYPE' => 'application/json'], $postJson);

        //verify
        $this->assertStatusCode(201, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonEquals($expectedResponse, $client->getResponse()->getContent(), ['created_at']);
        //check new user available through api
        $client->request('GET', '/users/4');
        $this->assertStatusCode(200, $client);
    }

    public function postSuccessfulUserDataProvider()
    {
        return [
            [
                '{"email": "foo@email.com", "first_name": "New", "last_name": "User", "password": "123qW"}',
                '{"id":4,"email":"foo@email.com","enabled":false,"first_name":"New","last_name":"User","created_at":"skipped"}'
            ],
            [
                '{"email": "bar@email.com", "first_name": "John", "last_name": "Smith", "password": "123qW"}',
                '{"id":4,"email":"bar@email.com","enabled":false,"first_name":"John","last_name":"Smith","created_at":"skipped"}'
            ],
        ];
    }

    /**
     * @dataProvider postFailsUserDataProvider
     * @param string $postJson
     * @param string $expectedResponse
     */
    public function testPostUserFail($postJson, $expectedResponse)
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('POST', '/users', [], [], ['CONTENT_TYPE' => 'application/json'], $postJson);

        //verify
        $this->assertStatusCode(400, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonEquals($expectedResponse, $client->getResponse()->getContent(), ['code']);
        //check new user was not created and not available through api
        $client->request('GET', '/users/4');
        $this->assertStatusCode(404, $client);
    }

    public function postFailsUserDataProvider()
    {
        return [
            //bad json
            ['', '{"error":{"message":"Could not decode JSON, syntax error - malformed JSON.","type":"common","details":[]}}'],
            //empty request
            [
                '{}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"email"},'
                    . '{"message":"This value should not be blank.","code":"skipped","name":"firstName"},'
                    . '{"message":"This value should not be blank.","code":"skipped","name":"lastName"},'
                    . '{"message":"This value should not be blank.","code":"skipped","name":"password"}'
                . ']}}'
            ],
            //first, last and password missing
            [
                '{"email": "foo@email.com"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"firstName"},'
                    . '{"message":"This value should not be blank.","code":"skipped","name":"lastName"},'
                    . '{"message":"This value should not be blank.","code":"skipped","name":"password"}'
                . ']}}'
            ],
            //last and password missing
            [
                '{"email": "foo@email.com", "first_name": "Foo"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"lastName"},'
                    . '{"message":"This value should not be blank.","code":"skipped","name":"password"}'
                . ']}}'
            ],
            //password missing
            [
                '{"email": "foo@email.com", "first_name": "Foo", "last_name": "Bar"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"password"}'
                . ']}}'
            ],
            //empty email
            [
                '{"email": null, "first_name": "Foo", "last_name": "Bar", "password": "123qW"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"email"}'
                . ']}}'
            ],
            //malformed email
            [
                '{"email": "foo", "first_name": "Foo", "last_name": "Bar", "password": "123qW"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value is not a valid email address.","code":"skipped","name":"email"}'
                . ']}}'
            ],
            //empty first
            [
                '{"email": "foo@email.com", "first_name": null, "last_name": "Bar", "password": "123qW"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"firstName"}'
                . ']}}'
            ],
            //empty last
            [
                '{"email": "foo@email.com", "first_name": "Foo", "last_name": null, "password": "123qW"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"lastName"}'
                . ']}}'
            ],
            //empty password
            [
                '{"email": "foo@email.com", "first_name": "Foo", "last_name": "Bar", "password": null}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value should not be blank.","code":"skipped","name":"password"}'
                . ']}}'
            ],
            //password not strict enough
            [
                '{"email": "foo@email.com", "first_name": "Foo", "last_name": "Bar", "password": "123"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"Password should be at least 5 characters long","code":"skipped","name":"password"},'
                    . '{"message":"Password should contain at least one letter in lower case","code":"skipped","name":"password"},'
                    . '{"message":"Password should contain at least one letter in upper case","code":"skipped","name":"password"}'
                . ']}}'
            ],
            //user with given email already exists
            [
                '{"email": "user-1@foo.com", "first_name": "Foo", "last_name": "Bar", "password": "123qW"}',
                '{"error":{"message":"Validation failed","type":"validation","details":['
                    . '{"message":"This value is already used.","code":"skipped","name":"email"}'
                . ']}}'
            ]
        ];
    }

    /**
     * @resetDatabase
     * @dataProvider patchSuccessfulDataProvider
     * @param int $id
     * @param string $patchJson
     * @param string $expectedResponse
     */
    public function testPatchUserSuccessful($id, $patchJson, $expectedResponse)
    {
        //create client
        $client = $this->makeClient();

        //call
        $client->request('PATCH', sprintf('/users/%s', $id), [], [], ['CONTENT_TYPE' => 'application/json'], $patchJson);

        //verify
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJsonEquals($expectedResponse, $client->getResponse()->getContent());
    }

    public function patchSuccessfulDataProvider()
    {
        return [
            //update email only
            [
                1,
                '{"email": "user-101@foo.com"}',
                '{"id":1,"email":"user-101@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
            ],
            //update first name only
            [
                1,
                '{"first_name": "Updated"}',
                '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Updated","last_name":"User","created_at":"2017-01-01 12:00:00"}'
            ],
            //update last name only
            [
                1,
                '{"last_name": "Updated"}',
                '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"Updated","created_at":"2017-01-01 12:00:00"}'
            ],
            //enable user
            [
                1,
                '{"enabled": true}',
                '{"id":1,"email":"user-1@foo.com","enabled":true,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
            ],
            //disable user
            [
                2,
                '{"enabled": false}',
                '{"id":2,"email":"user-2@foo.com","enabled":false,"first_name":"Enabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
            ],
            //change password
            [
                1,
                '{"password": "newPassword1"}',
                '{"id":1,"email":"user-1@foo.com","enabled":false,"first_name":"Disabled","last_name":"User","created_at":"2017-01-01 12:00:00"}'
            ],
            //update all fields
            [
                1,
                '{"email": "user-111@foo.com", "first_name": "First", "last_name": "User", "password": "newPassword123", "enabled": true}',
                '{"id":1,"email":"user-111@foo.com","enabled":true,"first_name":"First","last_name":"User","created_at":"2017-01-01 12:00:00"}'
            ],
        ];
    }
}
