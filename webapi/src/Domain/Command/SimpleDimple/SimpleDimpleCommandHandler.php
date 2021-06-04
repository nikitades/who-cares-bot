<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\Domain\Command\SimpleDimple;

use Longman\TelegramBot\Request;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SimpleDimpleCommandHandler implements MessageHandlerInterface
{
    public function __construct(
        private int $cachePeriod
    ) {
    }

    public function __invoke(SimpleDimpleCommand $command): void
    {
        Request::sendMessage([
            'chat_id' => -515375295,
            'text' => 'privet, cache is ' . $this->cachePeriod,
        ]);
    }
}
