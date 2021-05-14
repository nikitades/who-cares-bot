<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('Nikitades\WhoCaresBot\WebApi\\', __DIR__ . '/../src/')
        ->exclude([__DIR__ . '/../src/DependencyInjection/', __DIR__ . '/../src/Domain/', __DIR__ . '/../src/Kernel.php']);

    $services->load('Nikitades\WhoCaresBot\WebApi\App\Controller\\', __DIR__ . '/../src/App/Controller/')
        ->tag('controller.service_arguments');
};
