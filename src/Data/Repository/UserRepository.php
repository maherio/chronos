<?php

namespace Maherio\Chronos\Data\Repository;

use Maherio\Chronos\Data\Mapper\UserMapper;
use Maherio\Chronos\Data\Repository\Repository;

class UserRepository extends Repository {
    protected $entityClass = 'Maherio\Chronos\Data\Entity\User';

    public function __construct(UserMapper $mapper) {
        return parent::__construct($mapper);
    }
}
