<?php

declare(strict_types=1);

use Nikitades\WhoCaresBot\WebApi\App\Command\AsyncCommandInterface;
use Nikitades\WhoCaresBot\WebApi\App\Command\DetectPeak\DetectPeakCommand;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CalculateChatPeak\CalculateChatPeakCommand;
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
                'async_slow' => [
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'options' => [
                        'auto_setup' => false,
                    ],
                ],
            ],
            'routing' => [
                AsyncCommandInterface::class => 'async',
                CalculateChatPeakCommand::class => 'async_slow',
                DetectPeakCommand::class => 'async_slow',
            ],
            'default_bus' => 'command.bus',
            'buses' => [
                'command.bus' => [
                    'middleware' => [
                        // 'doctrine_transaction',
                        'doctrine_ping_connection',
                    ],
                ],
            ],
        ],
    ]);
};
