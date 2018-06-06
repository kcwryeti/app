<?php

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Entity\User;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UserControllerTest extends ApiTestCase
{

    public function testPOST()
    {
        $data = array(
            'username' => 'TestUser',
            'email' => 'tuser@example.com',
            'plainPassword' => 'foo',
            'roles' => ['ROLE_ADMIN']
        );

        $response = $this->client->post('/adm/api/user', [
            'body' => json_encode($data)
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $finishedData = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('username', $finishedData);
        $this->assertEquals('/adm/api/user/TestUser',$response->getHeader('Location'));

    }

    public function testGetUser()
    {
        $this->createUser([
            'username' => 'TestUser1',
            'email' => 'tuser1@example.com',
            'plainPassword' => 'foo',
            'roles' => ['ROLE_ADMIN'],
        ]);

        $response = $this->client->get('/adm/api/user/TestUser1');
        $this->assertEquals(200,$response->getStatusCode());
        $finishedData = json_decode($response->getBody(true),true);
        $this->assertArrayHasKey('roles',$finishedData);
    }

    public function testPUTUser()
    {
        $this->createUser([
            'username' => 'UserTester',
            'email' => 'tuser1@example.com',
            'plainPassword' => 'foo',
            'roles' => ['ROLE_USER'],
        ]);

        $data = [
            'username' => 'UserTester',
            'email' => 'tuser1@example.com',
            'plainPassword' => 'foo',
            'roles' => ['ROLE_ADMIN'],
        ];

        $response = $this->client->put('/adm/api/user/UserTester', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(200,$response->getStatusCode());
        $finishedData = json_decode($response->getBody(true),true);
    }

    public function testDELETEUser()
    {
        $this->createUser([
            'username' => 'UserTester',
            'email' => 'tuser1@example.com',
            'plainPassword' => 'foo',
            'roles' => ['ROLE_USER'],
        ]);

        $response = $this->client->delete('/adm/api/user/UserTester');
        $this->assertEquals(204,$response->getStatusCode());


        
    }

    public function testPATCHUser()
    {
        $this->createUser([
            'username' => 'UserTester',
            'email' => 'tuser1@example.com',
            'plainPassword' => 'foo',
            'roles' => ['ROLE_USER'],
        ]);

        $data = [
        ];

        $response = $this->client->patch('/adm/api/user/UserTester', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(200,$response->getStatusCode());
        $finishedData = json_decode($response->getBody(true),true);
        $this->assertEquals(["ROLE_USER"],$finishedData['roles']);


    }

    public function testValidationErrors()
    {
        $data = [
          'email' => 'tuser@example.com',
          'roles' => 'ROLE_ADMIN',
        ];

        $response = $this->client->post('/adm/api/user', [
            'body' => json_encode($data)
        ]);

        $finishedData = json_decode($response->getBody(true), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(['status','type','title','errors','detail'],array_keys($finishedData));
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type'));
    }

    public function testInvalidJson()
    {
        $invalidBody = <<<EOF
{
    "username" : "testUser
    "email": "tuser@example.com"
}
EOF;
        $response = $this->client->post('/adm/api/user', [
            'body' => $invalidBody
        ]);

        $finishedData = json_decode($response->getBody(true), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($finishedData['type'],'invalid_body_format' );
    }

    public function test404Exception()
    {
        $response = $this->client->get('/adm/api/fake');

        $finishedData = json_decode($response->getBody(true), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type'));
        $this->assertEquals($finishedData['type'],'about:blank' );
        $this->assertEquals($finishedData['title'],'Not Found' );
    }


    private function createUser(array $data)
    {
        $user = new User();
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($data as $key => $value) {
            $accessor->setValue($user, $key, $value);
        }

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

    }




}