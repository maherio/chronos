<?php

namespace Maherio\Chronos\Domain;

use Maherio\Chronos\Data\Repository\ShiftRepository;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;

class UpsertShift implements DomainInterface {
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
        $success = false;
        $output = [];

        $shift = $this->shiftRepository->find($input['id']);

        if($shift) {
            //update the shift
            $shift = $this->shiftRepository->update($shift, $input);
        } else {
            $shift = $this->shiftRepository->create($input);
        }

        $success = $this->shiftRepository->save($shift);

        if($success) {
            $status = PayloadInterface::STATUS_OK;
            $output = [
                'shifts' => $shift
            ];
        } else {
            $status = PayloadInterface::STATUS_BAD_REQUEST;
            $output = [
                'errors' => $shift->errors()
            ];
        }

        return $this->payload
            ->withStatus($status)
            ->withOutput($output);
    }
}
