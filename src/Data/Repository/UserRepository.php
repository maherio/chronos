<?php

namespace Maherio\Chronos\Data\Repository;

use Maherio\Chronos\Data\Mapper\UserMapper;
use Maherio\Chronos\Data\Entity\User;
use Equip\Data\Repository\RepositoryInterface;
use Equip\Data\Repository\CreateRepositoryInterface;
use Spot\Locator;
use DateTime;

class UserRepository implements RepositoryInterface, CreateRepositoryInterface {

    /**
     * The Spot Locator used for instantiating data mappers
     * @var \Spot\Locator
     */
    protected $dataMapper;

    public function __construct(UserMapper $mapper) {
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
        $criteria = $this->getValidatedUserValues($criteria);
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

        $criteria = $this->getValidatedUserValues($criteria);

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
     * Create a new object and return it
     *
     * @param array $values
     *
     * @return object
     */
    public function create(array $values) {
        $userValues = $this->getValidatedUserValues($values);
        return $this->dataMapper->create($userValues);
    }

    protected function getValidatedUserValues($proposedValues = []) {
        $validatedValues = [];
        foreach (User::fields() as $field => $fieldParameters) {
            if(array_key_exists($field, $proposedValues)) {
                if($fieldParameters['type'] == 'datetime') {
                    $validatedValues[$field] = new DateTime($proposedValues[$field]);
                } else {
                    $validatedValues[$field] = $proposedValues[$field];
                    settype($validatedValues[$field], $fieldParameters['type']);
                }
            }
        }
        return $validatedValues;
    }
}
