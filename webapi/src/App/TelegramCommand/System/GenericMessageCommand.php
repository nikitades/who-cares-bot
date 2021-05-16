<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\System;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomSystemCommand;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\RegisterMessage\RegisterMessageCommand;

class GenericMessageCommand extends AbstractCustomSystemCommand
{
    /**
     * {@inheritDoc}
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $this->dispatch(new RegisterMessageCommand(
            messageId: $message->getMessageId(),
            replyToMessageId: $message->getReplyToMessage()->getMessageId(),
            text: $message->getText() ?? '',
            userId: $message->getFrom()->getId(),
            chatId: $message->getChat()->getId(),
            stickedId: $message->getSticker()->getFileId(),
            attachType: $message->getType()
        ));

        return Request::sendMessage([
            'chat_id' => $message->getChat()->getId(),
            'text' => $message->getText() ?? $message->getType(),
        ]);
    }
}
