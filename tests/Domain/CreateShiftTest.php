<?php

use Maherio\Chronos\Domain\CreateShift;
use Equip\Payload;
use Equip\Adr\PayloadInterface;

class CreateShiftTest extends PHPUnit_Framework_TestCase {
    protected $testConfig;
    protected $createShiftDomain;

    protected function setUp() {
        $this->testConfig = new TestConfig;
        $payload = new Payload;
        $this->createShiftDomain = new CreateShift($payload, $this->testConfig->getShiftRepository());
    }

    protected function createShift($input) {
        $createShift = $this->createShiftDomain; //unfortunately php doesn't support {$this->variable}($input);
        return $createShift($input);
    }

    protected function compareShifts($shiftInput, $actualShift) {
        //required input
        $this->assertEquals($shiftInput['manager_id'], $actualShift->manager_id);
        $this->assertEquals($shiftInput['start_time'], $actualShift->start_time->format(DateTime::RFC2822));
        $this->assertEquals($shiftInput['end_time'], $actualShift->end_time->format(DateTime::RFC2822));

        //optional input
        if(array_key_exists('id', $shiftInput)) {
            $this->assertEquals($shiftInput['id'], $actualShift->id);
        } else {
            $this->assertNotNull($actualShift->id);
        }

        if(array_key_exists('employee_id', $shiftInput)) {
            $this->assertEquals($shiftInput['employee_id'], $actualShift->employee_id);
        } else {
            $this->assertNull($actualShift->employee_id);
        }

        if(array_key_exists('break', $shiftInput)) {
            $this->assertEquals($shiftInput['break'], $actualShift->break);
        } else {
            $this->assertNull($actualShift->break);
        }

        if(array_key_exists('created_at', $shiftInput)) {
            $this->assertEquals($shiftInput['created_at'], $actualShift->created_at->format(DateTime::RFC2822));
        } else {
            $this->assertNotNull($actualShift->created_at);
        }

        if(array_key_exists('updated_at', $shiftInput)) {
            $this->assertEquals($shiftInput['updated_at'], $actualShift->updated_at->format(DateTime::RFC2822));
        } else {
            $this->assertNotNull($actualShift->updated_at);
        }
    }

    public function testReturnsAPayload() {
        $input = [];
        $newPayload = $this->createShift($input);

        $this->assertInstanceOf(Payload::class, $newPayload);
    }

    //USER STORY 5
    public function testCreatesAShift() {
        $input = [
            'manager_id' => 1,
            'employee_id' => 4,
            'break' => 2.5,
            'start_time' => date(DateTime::RFC2822, strtotime('+10 days')),
            'end_time' => date(DateTime::RFC2822, strtotime('+11 days'))
        ];
        $newPayload = $this->createShift($input);
        $output = $newPayload->getOutput();

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
        $this->assertArrayHasKey('shifts', $output);
        $this->compareShifts($input, $output['shifts']);
    }

    public function testCreatesMinimalShift() {
        $input = [
            'manager_id' => 1,
            'start_time' => date(DateTime::RFC2822, strtotime('+20 days')),
            'end_time' => date(DateTime::RFC2822, strtotime('+21 days'))
        ];
        $newPayload = $this->createShift($input);
        $output = $newPayload->getOutput();

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
        $this->assertArrayHasKey('shifts', $output);
        $this->compareShifts($input, $output['shifts']);
    }
}
