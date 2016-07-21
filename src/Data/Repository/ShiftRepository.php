<?php

namespace Maherio\Chronos\Data\Repository;

use Maherio\Chronos\Data\Mapper\ShiftMapper;
use Maherio\Chronos\Data\Repository\Repository;

//Note: I'm not using the Equip's Create or Update repository interfaces because I want to create entities without
//saving them, plus save(entity) works perfectly with Spot.
class ShiftRepository extends Repository {
    protected $entityClass = 'Maherio\Chronos\Data\Entity\Shift';

    public function __construct(ShiftMapper $mapper) {
        return parent::__construct($mapper);
    }

    /**
     * Find multiple objects by variable criteria, but include related Manager resources
     *
     * @param array $criteria
     * @param array $order_by
     * @param integer $limit
     * @param integer $offset
     *
     * @return array|Traversable
     */
    public function findByWithManager(array $criteria, array $order_by = null, $limit = null, $offset = null) {
        $query = $this->dataMapper
            ->all()
            ->with('manager');

        return $this->genericFind($query, $criteria, $order_by, $limit, $offset);
    }
}
