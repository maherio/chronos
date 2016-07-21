<?php

use Maherio\Chronos\Domain\GetUsers;
use Equip\Payload;
use Equip\Adr\PayloadInterface;

class GetUsersTest extends PHPUnit_Framework_TestCase {
    protected $testConfig;
    protected $getUsersDomain;

    protected function setUp() {
        $this->testConfig = new TestConfig;
        $payload = new Payload;
        $this->getUsersDomain = new GetUsers($payload, $this->testConfig->getUserRepository());
    }

    protected function getUsers($input) {
        $getUsers = $this->getUsersDomain; //unfortunately php doesn't support {$this->variable}($input);
        return $getUsers($input);
    }

    protected function compareUsers($stubbedUser, $actualUser) {
        //only difference is datetime objects vs strings
        $this->assertEquals($stubbedUser['id'], $actualUser->id);
        $this->assertEquals($stubbedUser['name'], $actualUser->name);
        $this->assertEquals($stubbedUser['role'], $actualUser->role);
        $this->assertEquals($stubbedUser['email'], $actualUser->email);
        $this->assertEquals($stubbedUser['phone'], $actualUser->phone);
        $this->assertEquals($stubbedUser['created_at'], $actualUser->created_at->format(DateTime::RFC2822));
        $this->assertEquals($stubbedUser['updated_at'], $actualUser->updated_at->format(DateTime::RFC2822));
    }

    public function testReturnsAPayload() {
        $input = [];
        $newPayload = $this->getUsers($input);

        $this->assertInstanceOf(Payload::class, $newPayload);
    }

    public function testReturnsOkStatus() {
        $input = [];
        $newPayload = $this->getUsers($input);

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
    }

    //USER STORY 9
    public function testReturnsCorrectUser() {
        $input = [
            'id' => 3
        ];
        $newPayload = $this->getUsers($input);
        $output = $newPayload->getOutput();

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
        $this->assertCount(1, $output['users']);

        //loop through each stubbed user to get the correct user
        foreach ($this->testConfig->getUserData() as $expectedUser) {
            if($expectedUser['id'] == $input['id']) {
                $this->compareUsers($expectedUser, $output['users'][0]);
            }
        }
    }
}
