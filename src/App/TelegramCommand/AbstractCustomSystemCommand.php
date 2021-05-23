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

    protected MessageBusInterface $commandBus;

    public function __construct(
        Telegram $telegram,
        Update $update,
        MessageBusInterface $queryBus,
        MessageBusInterface $commandBus
    ) {
        parent::__construct($telegram, $update);
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
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
