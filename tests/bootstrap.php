<?php

require __DIR__ . '/../vendor/autoload.php';

use Spot\Config;
use Spot\Locator;
use Maherio\Chronos\Data\Mapper\ShiftMapper;
use Maherio\Chronos\Data\Mapper\UserMapper;
use Maherio\Chronos\Data\Repository\ShiftRepository;
use Maherio\Chronos\Data\Repository\UserRepository;

class TestConfig {
    protected $shiftRepository;
    protected $userRepository;

    public function __construct() {
        //set up database
        $locator = $this->setupDatabaseLocator();

        //set up repositories
        $shiftRepository = $this->createShiftRepository($locator);
        $userRepository = $this->createUserRepository($locator);

        $this->shiftRepository = $this->seedShiftRepository($shiftRepository);
        $this->userRepository = $this->seedUserRepository($userRepository);
    }

    protected function setupDatabaseLocator() {
        $config = new Config;
        $config->addConnection('sqlite', [
            'driver' => 'pdo_sqlite',
            'memory' => true,
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

    protected function createUserRepository(Locator $locator) {
        //create data mapper for it
        $userMapper = new UserMapper($locator, 'Maherio\Chronos\Data\Entity\User');

        //migrate the mapper
        $userMapper->migrate();

        //create the repository
        return new UserRepository($userMapper);
    }

    protected function seedShiftRepository($shiftRepository) {
        $shifts = $this->getShiftData();
        foreach ($shifts as $shiftValues) {
            $shift = $shiftRepository->create($shiftValues);
            $shiftRepository->save($shift);
        }
        return $shiftRepository;
    }

    protected function seedUserRepository($userRepository) {
        $users = $this->getUserData();
        foreach ($users as $userValues) {
            $user = $userRepository->create($userValues);
            $success = $userRepository->save($user);
        }

        return $userRepository;
    }

    public function getShiftRepository() {
        return $this->shiftRepository;
    }

    public function getUserRepository() {
        return $this->userRepository;
    }

    public function getShiftData() {
        return [
            [
                'id' => 1,
                'manager_id' => 1,
                'employee_id' => 3,
                'break' => 1.5,
                'start_time' => date(DateTime::RFC2822, 1465830000),
                'end_time' => date(DateTime::RFC2822, 1465831000),
                'created_at' => date(DateTime::RFC2822, 1465794021),
                'updated_at' => date(DateTime::RFC2822, 1465794021),
            ],
            [
                'id' => 2,
                'manager_id' => 1,
                'employee_id' => 4,
                'break' => 2.0,
                'start_time' => date(DateTime::RFC2822, 1465830050),
                'end_time' => date(DateTime::RFC2822, 1465830150),
                'created_at' => date(DateTime::RFC2822, 1465794050),
                'updated_at' => date(DateTime::RFC2822, 1465794050),
            ],
            [
                'id' => 3,
                'manager_id' => 2,
                'employee_id' => 3,
                'break' => 2.5,
                'start_time' => date(DateTime::RFC2822, 1465850000),
                'end_time' => date(DateTime::RFC2822, 1465851500),
                'created_at' => date(DateTime::RFC2822, 1465794075),
                'updated_at' => date(DateTime::RFC2822, 1465794075),
            ],
        ];
    }

    public function getUserData() {
        return [
            [
                'id' => 1,
                'name' => 'Jean Luc Picard',
                'role' => 'manager',
                'email' => 'TeaEarlGreyHot@UnitedFederationOfPlanets.com',
                'phone' => '0-123-456-7890',
                'created_at' => date(DateTime::RFC2822, 1465794000),
                'updated_at' => date(DateTime::RFC2822, 1465794001),
            ],
            [
                'id' => 2,
                'name' => 'Josiah Bartlett',
                'role' => 'manager',
                'email' => 'potus@usa.com',
                'phone' => '0-987-654-3210',
                'created_at' => date(DateTime::RFC2822, 1465794000),
                'updated_at' => date(DateTime::RFC2822, 1465794000),
            ],
            [
                'id' => 3,
                'name' => 'Sterling Archer',
                'role' => 'employee',
                'email' => 'duchess@isis.com',
                'phone' => '1-111-111-1111',
                'created_at' => date(DateTime::RFC2822, 1465794010),
                'updated_at' => date(DateTime::RFC2822, 1465794010),
            ],
            [
                'id' => 4,
                'name' => 'Anakin Skywalker',
                'role' => 'employee',
                'email' => 'waaaaah@empire.com',
                'phone' => '2-222-222-2222',
                'created_at' => date(DateTime::RFC2822, 1465794020),
                'updated_at' => date(DateTime::RFC2822, 1465794021),
            ],
        ];
    }
}
