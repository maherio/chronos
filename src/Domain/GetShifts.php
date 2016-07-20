<?php

namespace Maherio\Chronos\Domain;

use Maherio\Chronos\Data\Repository\ShiftRepository;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;

class GetShifts implements DomainInterface {
    /**
     * @var PayloadInterface
     */
    private $payload;

    /**
     * @param PayloadInterface $payload
     */
    public function __construct(PayloadInterface $payload, ShiftRepository $shiftRepository)
    {
        $this->payload = $payload;
        $this->shiftRepository = $shiftRepository;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        // $shifts = [
        //     (object)[
        //         'id' => 1,
        //         'manager' => 'test',
        //         'employee' => 'test',
        //         'break' => 'test',
        //         'start_time' => 'test',
        //         'end_time' => 'test',
        //         'created_at' => 'test',
        //         'updated_at' => 'test'
        //     ],
        //     (object)[
        //         'id' => 2,
        //         'manager' => 'test',
        //         'employee' => 'test',
        //         'break' => 'test',
        //         'start_time' => 'test',
        //         'end_time' => 'test',
        //         'created_at' => 'test',
        //         'updated_at' => 'test'
        //     ],
        // ];

        $shifts = $this->shiftRepository->findBy([]);
        return $this->payload
            ->withStatus(PayloadInterface::STATUS_OK)
            ->withOutput([
                'shifts' => $shifts
            ]);
    }
}
