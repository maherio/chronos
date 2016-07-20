<?php

require __DIR__ . '/../vendor/autoload.php';

use Spot\Config;
use Spot\Locator;
use Maherio\Chronos\Data\Mapper\ShiftMapper;
use Maherio\Chronos\Data\Repository\ShiftRepository;

class TestConfig {
    protected $shiftRepository;

    public function __construct() {
        //set up database
        $locator = $this->setupDatabaseLocator();

        //set up repositories
        $this->shiftRepository = $this->createShiftRepository($locator);

        //seed repository
        $this->seedRepositories();
    }

    protected function setupDatabaseLocator() {
        $config = new Config;
        $config->addConnection('sqlite', [
            'driver' => 'pdo_sqlite',
            // 'path' => $sqlitePath
            'memory' => true
        ]);
        return new Locator($config);
    }

    protected function createShiftRepository(Locator $locator) {
        //create data mapper for it
        $shiftMapper = new ShiftMapper($locator, 'Maherio\Chronos\Data\Entity\Shift');

        //migrate the mapper
        $shiftMapper->migrate();

        //create the repository
        return new ShiftRepository($shiftMapper);
    }

    protected function seedRepositories() {
        //seed shifts
        $shifts = $this->getShiftData();
        foreach ($shifts as $shiftValues) {
            $this->shiftRepository->create($shiftValues);
        }
    }

    public function getShiftData() {
        return [
            [
                'id' => 1,
                'manager_id' => 1,
                'employee_id' => 2,
                'break' => 1.5,
                'start_time' => date(DateTime::RFC2822, strtotime('+1 day')),
                'end_time' => date(DateTime::RFC2822, strtotime('+2 days')),
                'created_at' => date(DateTime::RFC2822, strtotime('-1 day')),
                'updated_at' => date(DateTime::RFC2822)
            ],
            [
                'id' => 2,
                'manager_id' => 1,
                'employee_id' => 3,
                'break' => 2.0,
                'start_time' => date(DateTime::RFC2822, strtotime('+2 days')),
                'end_time' => date(DateTime::RFC2822, strtotime('+3 days')),
                'created_at' => date(DateTime::RFC2822, strtotime('-1 hour')),
                'updated_at' => date(DateTime::RFC2822, strtotime('-1 minute'))
            ],
        ];
    }

    public function getShiftRepository() {
        return $this->shiftRepository;
    }
}
