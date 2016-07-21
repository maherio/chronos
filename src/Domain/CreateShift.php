<?php

namespace Maherio\Chronos\Domain;

use Maherio\Chronos\Data\Repository\ShiftRepository;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;

class CreateShift implements DomainInterface {
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
        $status = PayloadInterface::STATUS_INTERNAL_SERVER_ERROR;
        $output = [];

        try {
            $shift = $this->shiftRepository->create($input);
            $status = PayloadInterface::STATUS_OK;
            $output = [
                'shifts' => $shift
            ];
        } catch(\Exception $exception) {
            $status = PayloadInterface::STATUS_BAD_REQUEST; //we'll just assume bad input for now
            $output = [
                'error' => $exception->getMessage() //this leaks way to much internal stuff and isn't terribly helpful, but it will do for now
            ];
        }
        return $this->payload
            ->withStatus($status)
            ->withOutput($output);
    }
}
