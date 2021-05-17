<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand;

use Symfony\Component\Messenger\MessageBusInterface;

trait RenderMessageTrait
{
    use UnwrapTrait;

    protected MessageBusInterface $presenterBus;

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
    protected function renderMessage($message)
    {
        return $this->unwrap(
            $this->presenterBus->dispatch($message)
        );
    }
}
