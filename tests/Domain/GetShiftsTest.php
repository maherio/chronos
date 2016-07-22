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

    protected function checkUser($user) {
        $userData = $this->testConfig->getUserData();
        foreach ($userData as $stubbedUser) {
            if($stubbedUser['id'] == $user->id) {
                $this->assertEquals($stubbedUser['name'], $user->name);
                $this->assertEquals($stubbedUser['role'], $user->role);
                $this->assertEquals($stubbedUser['email'], $user->email);
                $this->assertEquals($stubbedUser['phone'], $user->phone);
                $this->assertEquals($stubbedUser['created_at'], $user->created_at->format(DateTime::RFC2822));
                $this->assertEquals($stubbedUser['updated_at'], $user->updated_at->format(DateTime::RFC2822));
            }
        }
    }

    protected function checkShiftExists($expectedShift, $shifts) {
        foreach ($shifts as $shift) {
            if($shift->id == $expectedShift['id']) {
                $this->compareShifts($expectedShift, $shift);
                return; //we found it, no need to keep going
            }
        }

        $this->assertTrue(false, 'Did not find expected shift in shifts array');
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

    //USER STORY 2
    public function testReturnsShiftsWithinTimeframeWithEmployees() {
        //hmm this test is too brittle for my liking... it requires knowledge of the stub dates. Oh well, it works for this
        $shiftStartTime = date(DateTime::RFC2822, 1465830000);
        $shiftEndTime = date(DateTime::RFC2822, 1465831000);

        $input = [
            'starts_before' => $shiftEndTime,
            'ends_after' => $shiftStartTime,
            'include_employee' => true
        ];
        $newPayload = $this->getShifts($input);
        $output = $newPayload->getOutput();

        $this->assertArrayHasKey('shifts', $output);

        foreach ($this->testConfig->getShiftData() as $shift) {
            if($shift['start_time'] <= $shiftEndTime && $shift['end_time'] >= $shiftStartTime) {
                //found a shift that should be returned
                $this->checkShiftExists($shift, $output['shifts']);
            }
        }

        foreach ($output['shifts'] as $shift) {
            if(!is_null($shift->employee_id)) {
                $this->checkUser($shift->employee);
            }
        }
    }

    //USER STORY 6
    public function testReturnsShiftsWithinTimeframe() {
        //hmm this test is too brittle for my liking... it requires knowledge of the stub dates. Oh well, it works for this
        $shiftStartTime = date(DateTime::RFC2822, 1465830000);
        $shiftEndTime = date(DateTime::RFC2822, 1465831000);

        $input = [
            'starts_before' => $shiftEndTime,
            'ends_after' => $shiftStartTime
        ];
        $newPayload = $this->getShifts($input);
        $output = $newPayload->getOutput();

        $this->assertArrayHasKey('shifts', $output);

        foreach ($this->testConfig->getShiftData() as $shift) {
            if($shift['start_time'] <= $shiftEndTime && $shift['end_time'] >= $shiftStartTime) {
                //found a shift that should be returned
                $this->checkShiftExists($shift, $output['shifts']);
            }
        }
    }


    //USER STORY 4
    public function testReturnsShiftsWithManager() {
        $input = [
            'employee_id' => 3,
            'include_manager' => true
        ];
        $newPayload = $this->getShifts($input);
        $output = $newPayload->getOutput();

        //loop through each returned shift and make sure the manager is correct
        foreach ($output['shifts'] as $actualShift) {
            if(!is_null($actualShift->manager_id)) {
                $this->assertNotNull($actualShift->manager);
                $this->checkUser($actualShift->manager);
            }
        }
    }
}
