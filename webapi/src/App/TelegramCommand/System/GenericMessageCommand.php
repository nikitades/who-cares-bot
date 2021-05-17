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
            replyToMessageId: $message->getReplyToMessage()?->getMessageId(), //@phpstan-ignore-line
            userId: $message->getFrom()->getId(),
            userNickname: $message->getFrom()->getUsername(),
            chatId: $message->getChat()->getId(),
            timestamp: $message->getDate(),
            text: $message->getText() ?? '',
            stickedId: $message->getSticker()?->getFileId(), //@phpstan-ignore-line
            attachType: $message->getType()
        ));

        return Request::sendMessage([
            'chat_id' => $message->getChat()->getId(),
            'text' => $message->getText() ?? $message->getType(),
        ]);
    }
}
