<?php

require __DIR__ . '/../vendor/autoload.php';

use Maherio\Chronos\Domain;
use Maherio\Chronos\Configuration\SpotConfiguration;

Equip\Application::build()
->setConfiguration([
    Equip\Configuration\AurynConfiguration::class,
    Equip\Configuration\DiactorosConfiguration::class,
    Equip\Configuration\PayloadConfiguration::class,
    Equip\Configuration\RelayConfiguration::class,
    Equip\Configuration\WhoopsConfiguration::class,
    Maherio\Chronos\Configuration\SpotConfiguration::class,
])
->setMiddleware([
    Relay\Middleware\ResponseSender::class,
    Equip\Handler\ExceptionHandler::class,
    Equip\Handler\DispatchHandler::class,
    Equip\Handler\JsonContentHandler::class,
    Equip\Handler\FormContentHandler::class,
    Equip\Handler\ActionHandler::class,
])
->setRouting(function (Equip\Directory $directory) {
    return $directory
    ->get('/', Domain\Welcome::class)
    ->get('/shifts', Domain\GetShifts::class) //1, 2, 3, 4, 6
    ->post('/shifts', Domain\CreateShift::class) //5
    ->put('/shifts/(shift)', Domain\UpsertShift::class) //7, 8
    ->get('/users', Domain\GetUsers::class) //9
    ;
})
->run();
