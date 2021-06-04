<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;

use Nikitades\WhoCaresBot\WebApi\Infrastructure\Local\LocalRenderedPageProvider;
use Nikitades\WhoCaresBot\WebApi\Infrastructure\Telegram\BusAwareTelegram;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$commandBus', service('command.bus'))
        ->bind('$cachePeriod', '%env(CACHE_PERIOD)%')
        ->bind('$peakSearchPeriod', '%env(PEAK_SEARCH_PERIOD)%');

    $services->instanceof(CommandHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus' => 'command.bus']);

    $services->load('Nikitades\WhoCaresBot\WebApi\\', __DIR__ . '/../src/')
        ->exclude([
            __DIR__ . '/../src/App/Controller/*',
            __DIR__ . '/../src/{Kernel.php}',
        ]);

    $services->load(
            'Nikitades\WhoCaresBot\WebApi\App\Controller\\',
            __DIR__ . '/../src/App/Controller/**/*{Controller.php}'
        )
        ->public();

    $services->set(BusAwareTelegram::class)
        ->arg('$api_key', '%env(BOT_TOKEN)%')
        ->arg('$bot_username', '%env(BOT_NAME)%')
        ->call('addCommandsPaths', [
            [
                __DIR__ . '/../src/App/TelegramCommand/System',
                __DIR__ . '/../src/App/TelegramCommand/User',
            ],
        ]);

    $services->set('local.node.httpClient', Client::class)
        ->arg('$config', [
            'base_uri' => 'http://localhost:9000',
        ]);

    $services->set(LocalRenderedPageProvider::class)
        ->arg(Client::class, service('local.node.httpClient'));
};
