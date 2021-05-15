<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\System;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\RegisterMessage\RegisterMessageCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class GenericMessageCommand extends SystemCommand
{
    /**
     * @param Telegram $telegram
     * @param Update $update
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        protected $telegram,
        protected $update,
        private MessageBusInterface $messageBus
    ) {
        parent::__construct($telegram, $update);
    }

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

        $this->messageBus->dispatch(new RegisterMessageCommand(
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
