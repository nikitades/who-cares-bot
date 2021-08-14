<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\System;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomSystemCommand;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\RegisterMessage\RegisterMessageCommand;
use Nikitades\WhoCaresBot\WebApi\App\Command\StartThrottledPeakDetection\StartThrottledPeakDetectionCommand;

class GenericMessageCommand extends AbstractCustomSystemCommand
{
    /**
     * {@inheritDoc}
     */
    public function execute(): ServerResponse
    {
        $this->dispatch(new StartThrottledPeakDetectionCommand($this->getMessage()->getChat()->getId()));

        $this->dispatch(new RegisterMessageCommand(
            messageId: $this->getMessage()->getMessageId(),
            replyToMessageId: $this->getMessage()->getReplyToMessage()?->getMessageId(), //@phpstan-ignore-line
            userId: $this->getMessage()->getFrom()->getId(),
            userNickname: $this->getMessage()->getFrom()->getUsername(),
            chatId: $this->getMessage()->getChat()->getId(),
            timestamp: $this->getMessage()->getDate(),
            text: $this->getMessage()->getText() ?? '',
            stickedId: $this->getMessage()->getSticker()?->getFileId(), //@phpstan-ignore-line
            attachType: $this->getMessage()->getType()
        ));

        return Request::emptyResponse();
    }
}
