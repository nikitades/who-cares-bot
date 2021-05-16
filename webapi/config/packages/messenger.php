<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'transports' => null,
            'routing' => null,
            'default_bus' => 'query.bus',
            'buses' => [
                'command.bus' => [
                    'middleware' => [
                        'doctrine_transaction',
                    ],
                ],
                'query.bus' => null,
            ],
        ],
    ]);
};
