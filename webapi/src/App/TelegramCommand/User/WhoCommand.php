<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class WhoCommand extends UserCommand
{
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        return Request::sendMessage([
            'chat_id' => $message->getChat()->getId(),
            'text' => 'who!',
        ]);
    }
}
