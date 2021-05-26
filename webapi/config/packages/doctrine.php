<?php

declare(strict_types=1);

use DoctrineExtensions\Query\Postgresql\DateTrunc;
use DoctrineExtensions\Query\Postgresql\ExtractFunction;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(resolve:DATABASE_URL)%',
        ],
    ]);

    $containerConfigurator->extension('doctrine', [
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'mappings' => [
                'Nikitades\WhoCaresBot' => [
                    'is_bundle' => false,
                    'type' => 'annotation',
                    'dir' => '%kernel.project_dir%/src/Domain',
                    'prefix' => 'Nikitades\WhoCaresBot\WebApi\Domain',
                    'alias' => 'Nikitades\WhoCaresBot\WebApi\Domain',
                ],
            ],
            'dql' => [
                'string_functions' => [
                    'DATE_TRUNC' => DateTrunc::class,
                    'EXTRACT' => ExtractFunction::class
                ]
            ]
        ],
    ]);
};
