<?php

namespace Maherio\Chronos\Domain;

use Maherio\Chronos\Data\Repository\ShiftRepository;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use DateTime;

class GetShifts implements DomainInterface {
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
        if(array_key_exists('starts_before', $input)) {
            $input['start_time <='] = $input['starts_before'];
        }
        if(array_key_exists('ends_after', $input)) {
            $input['end_time >='] = $input['ends_after'];
        }

        $relations = [];
        if(array_key_exists('include_manager', $input)) {
            $relations[] = 'manager';
        }
        if(array_key_exists('include_employee', $input)) {
            $relations[] = 'employee';
        }

        $shifts = $this->shiftRepository->findByWithRelations($relations, $input);

        return $this->payload
            ->withStatus(PayloadInterface::STATUS_OK)
            ->withOutput([
                'shifts' => $shifts
            ]);
    }
}
