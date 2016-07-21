<?php

namespace Maherio\Chronos\Domain;

use Maherio\Chronos\Data\Repository\ShiftRepository;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;

class GetHours implements DomainInterface {
    /**
     * @var PayloadInterface
     */
    private $payload;

    /**
     * @var ShiftRepository
     */
    protected $shiftRepository;

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
        $shifts = $this->shiftRepository->findBy($input);
        $totalHours = $this->getTotalHours($shifts);
        return $this->payload
            ->withStatus(PayloadInterface::STATUS_OK)
            ->withOutput([
                'hours_worked' => $totalHours
            ]);
    }

    /**
     * Get the total number of hours worked given the shifts
     * @param  [type] $shifts [description]
     * @return float         The total hours worked
     */
    protected function getTotalHours($shifts)
    {
        $totalHours = 0.0;

        foreach ($shifts as $shift) {
            $timeInterval = $shift->start_time->diff($shift->end_time);
            $totalHours += $timeInterval->format('%h');
            $totalHours += ($timeInterval->format('%i') / 60); //make sure to get minutes
        }

        return $totalHours;
    }
}
