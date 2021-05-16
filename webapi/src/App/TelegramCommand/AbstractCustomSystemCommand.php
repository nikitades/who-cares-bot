<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use function Safe\sprintf;

abstract class AbstractCustomSystemCommand extends UserCommand
{
    public function __construct(
        Telegram $telegram,
        Update $update,
        protected MessageBusInterface $queryBus,
        protected MessageBusInterface $commandBus
    ) {
        parent::__construct($telegram, $update);
    }

    protected function dispatch(object $command): void
    {
        $this->commandBus->dispatch($command);
    }

    /**
     * {@inheritDoc}
     */
    abstract public function execute(): ServerResponse;

    /**
     * Dispatches the given message, expecting to be handled by a single handler
     * and returns the result from the handler returned value.
     * This behavior is useful for both synchronous command & query buses,
     * the last one usually returning the handler result.
     *
     * @param object|Envelope $message The message or the message pre-wrapped in an envelope
     *
     * @return mixed The handler returned value
     */
    protected function handle($message)
    {
        $envelope = $this->queryBus->dispatch($message);
        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if (0 === count($handledStamps)) {
            throw new LogicException(sprintf('Message of type "%s" was handled zero times. Exactly one handler is expected when using "%s::%s()".', get_debug_type($envelope->getMessage()), static::class, __FUNCTION__));
        }

        if (\count($handledStamps) > 1) {
            $handlers = implode(', ', array_map(function (HandledStamp $stamp): string {
                return sprintf('"%s"', $stamp->getHandlerName());
            }, $handledStamps));

            throw new LogicException(sprintf('Message of type "%s" was handled multiple times. Only one handler is expected when using "%s::%s()", got %d: %s.', get_debug_type($envelope->getMessage()), static::class, __FUNCTION__, \count($handledStamps), $handlers));
        }

        return $handledStamps[0]->getResult();
    }
}
