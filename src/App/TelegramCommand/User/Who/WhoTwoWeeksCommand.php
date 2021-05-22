<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\Command\GenerateWho\GenerateWhoCommand;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomUserCommand;

class WhoTwoWeeksCommand extends AbstractCustomUserCommand
{
    public function execute(): ServerResponse
    {
        $this->dispatch(new GenerateWhoCommand(
            $this->getMessage()->getChat()->getId(),
            $this->getMessage()->getFrom()->getId(),
            14
        ));

        return Request::emptyResponse();
    }
}
