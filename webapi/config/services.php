<?php

declare(strict_types=1);

use Nikitades\WhoCaresBot\WebApi\Domain\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay\WhoDayQuery;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay\WhoDayQueryHandler;
use Nikitades\WhoCaresBot\WebApi\Domain\QueryHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Infrastructure\Longman\BusAwareTelegram;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

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

    $services->instanceof(CommandHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus' => 'command.bus']);

    $services->instanceof(QueryHandlerInterface::class)
        ->tag('messenger.message_handler', ['bus' => 'query.bus']);

    // $services->set(WhoDayQueryHandler::class)
    //     ->tag('messenger.message_handler', ['bus' => 'query.bus']);
};
