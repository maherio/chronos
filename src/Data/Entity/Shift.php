<?php

namespace Maherio\Chronos\Data\Entity;

use Spot\MapperInterface;
use Spot\Entity;
use Spot\EntityInterface;
use DateTime;

class Shift extends Entity {
    protected static $table = 'shifts';
    protected static $mapper = 'Maherio\Chronos\Data\Mapper\ShiftMapper';

    public static $managerKey = 'manager_id';
    public static $employeeKey = 'employee_id';

    public static function fields()
    {
        return [
            'id'                 => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            static::$managerKey  => ['type' => 'integer'],
            static::$employeeKey => ['type' => 'integer'],
            'break'              => ['type' => 'float'],
            'start_time'         => ['type' => 'datetime', 'required' => true, 'index' => true],
            'end_time'           => ['type' => 'datetime', 'required' => true, 'index' => true],
            'created_at'         => ['type' => 'datetime', 'value' => new DateTime()],
            'updated_at'         => ['type' => 'datetime', 'value' => new DateTime()]
        ];
    }

    public static function relations(MapperInterface $mapper, EntityInterface $entity)
    {
        return [
            'manager' => $mapper->belongsTo($entity, 'Maherio\Chronos\Data\Entity\User', static::$managerKey),
            'employee' => $mapper->belongsTo($entity, 'Maherio\Chronos\Data\Entity\User', static::$employeeKey)
        ];
    }
}
