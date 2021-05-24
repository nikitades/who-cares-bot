<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\GenerateWho\GenerateWhoCommand;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomUserCommand;

class WhoCommand extends AbstractCustomUserCommand
{
    public function execute(): ServerResponse
    {
        $this->dispatch(new GenerateWhoCommand(
            $this->getMessage()->getChat()->getId(),
            $this->getMessage()->getFrom()->getId(),
            1
        ));

        return Request::emptyResponse();
    }
}
