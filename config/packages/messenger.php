<?php

declare(strict_types=1);

use Nikitades\WhoCaresBot\WebApi\App\Command\GenerateTop\GenerateTopCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'transports' => [
                'sync' => 'sync://',
                'async' => [
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'options' => [
                        'auto_setup' => false,
                    ],
                ],
            ],
            'routing' => [
                GenerateTopCommand::class => 'async',
            ],
            'default_bus' => 'query.bus',
            'buses' => [
                'command.bus' => [
                    'middleware' => [
                        'doctrine_transaction',
                        'doctrine_ping_connection',
                    ],
                ],
                'query.bus' => null,
            ],
        ],
    ]);
};
