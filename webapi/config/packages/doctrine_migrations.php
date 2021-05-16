<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine_migrations', [
        'migrations_paths' => [
            'Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Migration' => '%kernel.project_dir%/src/Infrastructure/Doctrine/Migration',
        ],
    ]);

    $containerConfigurator->extension('doctrine_migrations', [
        'enable_profiler' => '%kernel.debug%',
    ]);
};
