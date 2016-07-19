<?php

// use PHPUnit\Framework\TestCase;

use Maherio\Chronos\Domain\GetShifts;
use Maherio\Chronos\Data\Repository\ShiftRepository;
use Equip\Payload;
use Equip\Adr\PayloadInterface;

class GetShiftsTest extends PHPUnit_Framework_TestCase {
    protected $getShiftsDomain;

    protected function setUp() {
        $payload = new Payload();
        $shiftRepository = new ShiftRepository();
        $this->getShiftsDomain = new GetShifts($payload, $shiftRepository);
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
}
