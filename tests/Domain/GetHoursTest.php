<?php

use Maherio\Chronos\Domain\GetHours;
use Equip\Payload;
use Equip\Adr\PayloadInterface;

class GetHoursTest extends PHPUnit_Framework_TestCase {
    protected $testConfig;
    protected $getHoursDomain;

    protected function setUp() {
        $this->testConfig = new TestConfig;
        $payload = new Payload;
        $this->getHoursDomain = new GetHours($payload, $this->testConfig->getShiftRepository());
    }

    protected function getHours($input) {
        $getHours = $this->getHoursDomain; //unfortunately php doesn't support {$this->variable}($input);
        return $getHours($input);
    }

    public function testReturnsAPayload() {
        $input = [];
        $newPayload = $this->getHours($input);

        $this->assertInstanceOf(Payload::class, $newPayload);
    }

    public function testReturnsOkStatus() {
        $input = [
            'employee_id' => 3
        ];
        $newPayload = $this->getHours($input);

        $this->assertEquals(PayloadInterface::STATUS_OK, $newPayload->getStatus());
    }

    //USER STORY 3
    public function testReturnsCorrectHours() {
        $input = [
            'employee_id' => 3
        ];
        $newPayload = $this->getHours($input);
        $output = $newPayload->getOutput();

        $this->assertArrayHasKey('hours_worked', $output);

        $expectedHours = 0.0;

        //loop through each of this user's shift and sum the hours worked
        foreach ($this->testConfig->getShiftData() as $shift) {
            if($shift['employee_id'] == $input['employee_id']) {
                $startTime = new DateTime($shift['start_time']);
                $endTime = new DateTime($shift['end_time']);
                $timeInterval = $endTime->diff($startTime);
                $expectedHours += $timeInterval->format('%h');
                $expectedHours += ($timeInterval->format('%i') / 60);
            }
        }

        $this->assertEquals($expectedHours, $output['hours_worked']);
    }
}
