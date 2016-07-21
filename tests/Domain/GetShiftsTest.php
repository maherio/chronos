<?php

use Maherio\Chronos\Domain\GetShifts;
use Equip\Payload;
use Equip\Adr\PayloadInterface;

class GetShiftsTest extends PHPUnit_Framework_TestCase {
    protected $testConfig;
    protected $getShiftsDomain;

    protected function setUp() {
        $this->testConfig = new TestConfig;
        $payload = new Payload;
        $this->getShiftsDomain = new GetShifts($payload, $this->testConfig->getShiftRepository());
    }

    protected function getShifts($input) {
        $getShifts = $this->getShiftsDomain; //unfortunately php doesn't support {$this->variable}($input);
        return $getShifts($input);
    }

    protected function compareShifts($stubbedShift, $actualShift) {
        //only difference is datetime objects vs strings
        $this->assertEquals($stubbedShift['id'], $actualShift->id);
        $this->assertEquals($stubbedShift['manager_id'], $actualShift->manager_id);
        $this->assertEquals($stubbedShift['employee_id'], $actualShift->employee_id);
        $this->assertEquals($stubbedShift['break'], $actualShift->break);
        $this->assertEquals($stubbedShift['start_time'], $actualShift->start_time->format(DateTime::RFC2822));
        $this->assertEquals($stubbedShift['end_time'], $actualShift->end_time->format(DateTime::RFC2822));
        $this->assertEquals($stubbedShift['created_at'], $actualShift->created_at->format(DateTime::RFC2822));
        $this->assertEquals($stubbedShift['updated_at'], $actualShift->updated_at->format(DateTime::RFC2822));
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

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
        $this->assertArrayHasKey('shifts', $output);
        $this->assertCount($expectedCount, $output['shifts']);
    }

    //USER STORY 1
    public function testReturnsShiftsForUser() {
        $input = [
            'employee_id' => 3
        ];
        $newPayload = $this->getShifts($input);
        $output = $newPayload->getOutput();

        $expectedCount = 0;
        $actualCount = 0;

        //loop through each stubbed shift to get the expected shifts
        foreach ($this->testConfig->getShiftData() as $expectedShift) {
            if($expectedShift['employee_id'] == $input['employee_id']) {
                ++$expectedCount;

                //get the matched shift
                foreach ($output['shifts'] as $actualShift) {
                    if($expectedShift['id'] == $actualShift->id) {
                        //found our match, increment and let's compare the two
                        ++$actualCount;
                        $this->compareShifts($expectedShift, $actualShift);
                        continue 2;
                    }
                }
            }
        }

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
        $this->assertEquals($expectedCount, $actualCount);
    }
}
