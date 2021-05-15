<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\System;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class GenericMessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * {@inheritDoc}
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        return new ServerResponse([
            'ok' => true,
            'chat_id' => $message->getChat()->getId(),
            'text' => $message->getText(),
        ]);
    }
}
