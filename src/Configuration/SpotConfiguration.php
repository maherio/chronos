<?php

namespace Maherio\Chronos\Configuration;

use Auryn\Injector;
use Equip\Configuration\ConfigurationInterface;
use Spot\Config;
use Spot\Locator;

class SpotConfiguration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function apply(Injector $injector)
    {
        //set up custom data mappers
        $shiftMapper = 'Maherio\Chronos\Data\Mapper\ShiftMapper';
        $shiftEntity = 'Maherio\Chronos\Data\Entity\Shift';
        $injector->define($shiftMapper, [':entityName' => $shiftEntity]);

        //set up database connection
        $injector->delegate('Spot\Locator', function(Config $config) {
            $sqlitePath = __DIR__ . '/../Database/database.sqlite';
            $config->addConnection('sqlite', [
                'driver' => 'pdo_sqlite',
                'path' => $sqlitePath
            ]);
            return new Locator($config);
        });
    }
}
