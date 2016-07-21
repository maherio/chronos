<?php

namespace Maherio\Chronos\Data\Entity;

use Maherio\Chronos\Data\Entity\Shift;
use Spot\MapperInterface;
use Spot\Entity;
use Spot\EntityInterface;
use DateTime;

class User extends Entity {
    protected static $table = 'users';
    protected static $mapper = 'Maherio\Chronos\Data\Mapper\UserMapper';

    public static function fields()
    {
        return [
            'id'           => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'name'         => ['type' => 'string', 'required' => true],
            'role'         => ['type' => 'string', 'required' => true, 'default' => 'employee'],
            'email'        => ['type' => 'string', 'required' => true, 'index' => true],
            'phone'        => ['type' => 'string'],
            'created_at'   => ['type' => 'datetime', 'value' => new DateTime()],
            'updated_at'   => ['type' => 'datetime', 'value' => new DateTime()]
        ];
    }

    public static function relations(MapperInterface $mapper, EntityInterface $entity)
    {
        return [
            'shifts' => $mapper->hasMany($entity, 'Maherio\Chronos\Data\Entity\Shift', Shift::$employeeKey)
        ];
    }
}
