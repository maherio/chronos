<?php

namespace Maherio\Chronos\Data\Repository;

use Equip\Data\Repository\RepositoryInterface;

class ShiftRepository implements RepositoryInterface {

    protected $shifts = [];

    public function __construct() {
        //just stub out something to return for now
        $this->shifts[] = (object)[
            'id' => 1,
            'manager' => 'test',
            'employee' => 'test',
            'break' => 'test',
            'start_time' => 'test',
            'end_time' => 'test',
            'created_at' => 'test',
            'updated_at' => 'test'
        ];
        $this->shifts[] = (object)[
            'id' => 2,
            'manager' => 'test',
            'employee' => 'test',
            'break' => 'test',
            'start_time' => 'test',
            'end_time' => 'test',
            'created_at' => 'test',
            'updated_at' => 'test'
        ];
    }

    /**
     * Find a single object by identifier
     *
     * @param mixed $id
     *
     * @return object|null
     */
    public function find($id) {
        return $this->shifts;
    }

    /**
     * Find multiple objects by their identifiers
     *
     * @param array $ids
     *
     * @return array|Traversable
     */
    public function findByIds(array $ids) {
        return $this->shifts;
    }

    /**
     * Find a single object by variable criteria
     *
     * @param array $criteria
     *
     * @return object|null
     */
    public function findOneBy(array $criteria) {
        return $this->shifts;
    }

    /**
     * Find multiple objects by variable criteria
     *
     * @param array $criteria
     * @param array $order_by
     * @param integer $limit
     * @param integer $offset
     *
     * @return array|Traversable
     */
    public function findBy(array $criteria, array $order_by = null, $limit = null, $offset = null) {
        return $this->shifts;
    }
}
