<?php

namespace Maherio\Chronos\Domain;

use Maherio\Chronos\Data\Repository\UserRepository;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;

class GetUsers implements DomainInterface {
    /**
     * @var PayloadInterface
     */
    private $payload;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @param PayloadInterface $payload
     */
    public function __construct(PayloadInterface $payload, UserRepository $userRepository)
    {
        $this->payload = $payload;
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $users = $this->userRepository->findBy($input);
        return $this->payload
            ->withStatus(PayloadInterface::STATUS_OK)
            ->withOutput([
                'users' => $users
            ]);
    }
}
