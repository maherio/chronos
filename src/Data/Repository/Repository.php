<?php

namespace Maherio\Chronos\Data\Repository;

use Maherio\Chronos\Data\Mapper\ShiftMapper;
use Maherio\Chronos\Data\Entity\Shift;
use Equip\Data\Repository\RepositoryInterface;
use Spot\Entity;
use Spot\Locator;
use Spot\Mapper;
use Spot\Query;
use DateTime;

//Note: I'm not using the Equip's Create or Update repository interfaces because I want to create entities without
//saving them, plus save(entity) works perfectly with Spot.
abstract class Repository implements RepositoryInterface {

    /**
     * The Spot Locator used for instantiating data mappers
     * @var \Spot\Locator
     */
    protected $dataMapper;

    protected $entityClass;

    public function __construct(Mapper $mapper) {
        $this->dataMapper = $mapper;
        $this->dataMapper->migrate();
    }

    /**
     * Find a single object by identifier
     *
     * @param mixed $id
     *
     * @return object|null
     */
    public function find($id) {
        return $this->dataMapper->get($id);
    }

    /**
     * Find multiple objects by their identifiers
     *
     * @param array $ids
     *
     * @return array|Traversable
     */
    public function findByIds(array $ids) {
        return $this->dataMapper
            ->where(['id' => $ids])
            ->execute();;
    }

    /**
     * Find a single object by variable criteria
     *
     * @param array $criteria
     *
     * @return object|null
     */
    public function findOneBy(array $criteria) {
        $criteria = $this->getValidatedValues($criteria);
        return $this->dataMapper
            ->where($criteria)
            ->execute();
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
        $query = $this->dataMapper->select();

        return $this->genericFind($query, $criteria, $order_by, $limit, $offset);
    }

    protected function genericFind(Query $query, array $criteria, array $order_by = null, $limit = null, $offset = null) {
        $criteria = $this->getValidatedValues($criteria);

        if(!empty($criteria)) {
            $query = $query->where($criteria);
        } else if(!is_null($order_by)) {
            $query = $query->order($order_by);
        } else if(!is_null($limit)) {
            $query = $query->limit($limit);
        } else if(!is_null($offset)) {
            $query = $query->offset($offset);
        }

        return $query->execute();
    }

    /**
     * Create a new object and return it. Note, it does not automatically save it.
     *
     * @param array $values
     *
     * @return object
     */
    public function create(array $values) {
        $validatedValues = $this->getValidatedValues($values);
        return $this->dataMapper->build($validatedValues);
    }

    /**
     * Saves an object (inserts if it didn't exist, updates if it did).
     *
     * @param Entity $entity
     *
     * @return boolean  True if update was successful, false if not
     */
    public function save(Entity $entity) {
        return $this->dataMapper->save($entity);
    }

    /**
     * Updates an entity with validated values.
     * @param  Entity $entity The entity to update
     * @param  array  $values The values to set
     * @return Entity         The updated entity
     */
    public function update(Entity $entity, array $values) {
        //not sure why the Spot entity doesn't have an option for this
        $validatedValues = $this->getValidatedValues($values);
        return $entity->data($validatedValues);
    }

    protected function getValidatedValues($proposedValues = []) {
        $validatedValues = [];
        $entityClass = $this->entityClass;
        foreach ($entityClass::fields() as $field => $fieldParameters) {
            //loop through to check comparison operators, which just need to start with the field
            foreach ($proposedValues as $proposedKey => $value) {
                if(strpos($proposedKey, $field) === 0) {
                    if($fieldParameters['type'] == 'datetime') {
                        $validatedValues[$proposedKey] = new DateTime($proposedValues[$proposedKey]);
                    } else {
                        $validatedValues[$proposedKey] = $proposedValues[$proposedKey];
                        settype($validatedValues[$field], $fieldParameters['type']);
                    }
                }
            }
        }

        return $validatedValues;
    }
}
