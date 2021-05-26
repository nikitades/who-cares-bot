<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'rate_limiter' => [
            'anonymous_api' => [
                'policy' => 'fixed_window',
                'limit' => 1,
                'interval' => '5 minutes',
            ],
        ],
    ]);
};
