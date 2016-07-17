<?php

// use PHPUnit\Framework\TestCase;

use Maherio\Chronos\Domain\Welcome;
use Equip\Payload;
use Equip\Adr\PayloadInterface;

class WelcomeTest extends PHPUnit_Framework_TestCase {
    protected $welcomeDomain;

    protected function setUp() {
        $payload = new Payload();
        $this->welcomeDomain = new Welcome($payload);
    }

    public function testReturnsAPayload() {
        $input = [];
        $welcome = $this->welcomeDomain; //unfortunately php doesn't support {$this->welcomeDomain}($input);
        $newPayload = $welcome($input);

        $this->assertInstanceOf(Payload::class, $newPayload);
    }

    public function testReturnsOkStatus() {
        $input = [];
        $welcome = $this->welcomeDomain;
        $newPayload = $welcome($input);

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
    }
}
