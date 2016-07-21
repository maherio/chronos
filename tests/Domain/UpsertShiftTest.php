<?php

use Maherio\Chronos\Domain\UpsertShift;
use Equip\Payload;
use Equip\Adr\PayloadInterface;

class UpsertShiftTest extends PHPUnit_Framework_TestCase {
    protected $testConfig;
    protected $upsertShiftDomain;

    protected function setUp() {
        $this->testConfig = new TestConfig;
        $payload = new Payload;
        $this->upsertShiftDomain = new UpsertShift($payload, $this->testConfig->getShiftRepository());
    }

    protected function upsertShift($input) {
        $upsertShift = $this->upsertShiftDomain; //unfortunately php doesn't support {$this->variable}($input);
        return $upsertShift($input);
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
        $input = [
            'id' => 1
        ];
        $newPayload = $this->upsertShift($input);

        $this->assertInstanceOf(Payload::class, $newPayload);
    }

    //USER STORY 5
    public function testCreatesAShift() {
        $input = [
            'id' => 25,
            'manager_id' => 1,
            'employee_id' => 4,
            'break' => 2.5,
            'start_time' => date(DateTime::RFC2822, strtotime('+10 days')),
            'end_time' => date(DateTime::RFC2822, strtotime('+11 days'))
        ];
        $newPayload = $this->upsertShift($input);
        $output = $newPayload->getOutput();

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
        $this->assertArrayHasKey('shifts', $output);
        $this->compareShifts($input, $output['shifts']);
    }

    //USER STORY 7
    public function testUpdatesShiftTime() {
        //first create a new one
        $input = [
            'id' => 26,
            'manager_id' => 1,
            'start_time' => date(DateTime::RFC2822, strtotime('+20 days')),
            'end_time' => date(DateTime::RFC2822, strtotime('+21 days'))
        ];
        $newPayload = $this->upsertShift($input);
        $output = $newPayload->getOutput();

        //now update the time
        $secondInput = [
            'id' => 26,
            'start_time' => date(DateTime::RFC2822),
            'end_time' => date(DateTime::RFC2822, strtotime('+8 hours')),
        ];
        $newPayload = $this->upsertShift($secondInput);
        $output = $newPayload->getOutput();

        //make sure it's actually updating an existing shift
        $expectedShift = array_merge($input, $secondInput);

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
        $this->assertArrayHasKey('shifts', $output);
        $this->compareShifts($expectedShift, $output['shifts']);
    }

    //USER STORY 8
    public function testUpdatesShiftEmployee() {
        //first create a new one
        $input = [
            'id' => 26,
            'manager_id' => 1,
            'employee_id' => 3,
            'start_time' => date(DateTime::RFC2822, strtotime('+20 days')),
            'end_time' => date(DateTime::RFC2822, strtotime('+21 days'))
        ];
        $newPayload = $this->upsertShift($input);
        $output = $newPayload->getOutput();

        //now update the employee
        $secondInput = [
            'id' => 26,
            'employee_id' => 4,
        ];
        $newPayload = $this->upsertShift($secondInput);
        $output = $newPayload->getOutput();

        //make sure it's actually updating an existing shift
        $expectedShift = array_merge($input, $secondInput);

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
        $this->assertArrayHasKey('shifts', $output);
        $this->compareShifts($expectedShift, $output['shifts']);
    }
}
