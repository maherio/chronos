<?php

// use PHPUnit\Framework\TestCase;

use Maherio\Chronos\Domain\GetShifts;
use Maherio\Chronos\Data\Repository\ShiftRepository;
use Equip\Payload;
use Equip\Adr\PayloadInterface;
use Spot\Config;
use Spot\Locator;

class GetShiftsTest extends PHPUnit_Framework_TestCase {
    protected $testConfig;
    protected $getShiftsDomain;

    protected function setUp() {
        $this->testConfig = new TestConfig;
        $payload = new Payload;
        $this->getShiftsDomain = new GetShifts($payload, $this->testConfig->getShiftRepository());
    }

    protected function getShifts($input) {
        $getShifts = $this->getShiftsDomain; //unfortunately php doesn't support {$this->welcomeDomain}($input);
        return $getShifts($input);
    }

    public function testReturnsAPayload() {
        $input = [];
        $newPayload = $this->getShifts($input);

        $this->assertInstanceOf(Payload::class, $newPayload);
    }

    public function testReturnsOkStatus() {
        $input = [];
        $newPayload = $this->getShifts($input);

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
    }

    public function testReturnsCorrectAmountOfShifts() {
        $input = [];
        $newPayload = $this->getShifts($input);
        $output = $newPayload->getOutput();
        $expectedCount = count($this->testConfig->getShiftData());


        $this->assertArrayHasKey('shifts', $output);
        $this->assertCount($expectedCount, $output['shifts']);
    }
}
