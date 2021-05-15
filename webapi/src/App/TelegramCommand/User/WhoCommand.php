<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay\WhoDayQuery;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay\WhoDayQueryResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class WhoCommand extends UserCommand
{
    use HandleTrait;

    public function __construct(
        Telegram $telegram,
        Update $update,
        MessageBusInterface $messageBus
    ) {
        parent::__construct($telegram, $update);
        $this->messageBus = $messageBus;
    }

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        /** @var WhoDayQueryResponse $response */
        $response = $this->handle(new WhoDayQuery(
                $message->getChat()->getId(),
                $message->getFrom()->getId()
            )
        );

        return Request::sendMessage([
            'chat_id' => $message->getChat()->getId(),
            'text' => implode(
                "\n",
                array_map(
                    fn (UserPosition $userPosition): string => sprintf('%s: %s', $userPosition->userNickname, $userPosition->userMessagesCount),
                    $response->userPositions
                ),
            ),
        ]);
    }
}