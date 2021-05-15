<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\RegisterMessage;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RegisterMessageCommandHandler implements MessageHandlerInterface
{
    public function __invoke(RegisterMessageCommand $command): void
    {
        //TODO: register the message in DB. Call the repositories etc
    }
}
