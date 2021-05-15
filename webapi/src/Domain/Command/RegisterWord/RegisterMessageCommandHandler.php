<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\RegisterMessage;

use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;

class RegisterMessageCommandHandler implements CommandHandlerInterface
{
    public function __invoke(RegisterMessageCommand $command): void
    {
        dump([]);
    }
}
