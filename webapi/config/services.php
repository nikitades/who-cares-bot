<?php

declare(strict_types=1);

use Nikitades\WhoCaresBot\WebApi\Infrastructure\Longman\ContainerizedTelegram;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('Nikitades\WhoCaresBot\WebApi\\', __DIR__ . '/../src/')
        ->exclude([
            __DIR__ . '/../src/App/Controller/*',
            __DIR__ . '/../src/Domain/',
            __DIR__ . '/../src/{Kernel.php}',
        ]);

    $services->load(
            'Nikitades\WhoCaresBot\WebApi\App\Controller\\',
            __DIR__ . '/../src/App/Controller/**/*{Controller.php}'
        )
        ->public();

    $services->set(ContainerizedTelegram::class)
        ->arg('$api_key', '%env(BOT_TOKEN)%')
        ->arg('$bot_username', '%env(BOT_NAME)%')
        ->call('addCommandsPath', [__DIR__ . '/../src/App/TelegramCommand/System']);
};
