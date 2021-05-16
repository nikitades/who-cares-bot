<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(resolve:DATABASE_URL)%',
        ],
    ]);

    //TODO: сделать чтобы доктрина сгенерировала стартовую миграцию
    $containerConfigurator->extension('doctrine', [
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'mappings' => [
                'Nikitades\WhoCaresBot\WebApi\Domain\\' => [
                    'is_bundle' => false,
                    'type' => 'annotation',
                    'dir' => '%kernel.project_dir%/src/Domain',
                    'prefix' => 'Nikitades\WhoCaresBot\WebApi\Domain',
                    'alias' => 'Nikitades\WhoCaresBot\WebApi\Domain',
                ],
            ],
        ],
    ]);
};
