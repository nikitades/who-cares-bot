<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class AbstractCustomSystemCommand extends UserCommand
{
    use HandleTrait;
    use RenderMessageTrait;

    protected MessageBusInterface $commandBus;

    public function __construct(
        Telegram $telegram,
        Update $update,
        MessageBusInterface $queryBus,
        MessageBusInterface $commandBus,
        MessageBusInterface $presenterBus
    ) {
        parent::__construct($telegram, $update);
        $this->presenterBus = $queryBus;
        $this->commandBus = $commandBus;
        $this->presenterBus = $presenterBus;
    }

    protected function dispatch(object $command): void
    {
        $this->commandBus->dispatch($command);
    }

    /**
     * {@inheritDoc}
     */
    abstract public function execute(): ServerResponse;
}
