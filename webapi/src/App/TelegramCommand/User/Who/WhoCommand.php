<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomUserCommand;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay\WhoDayQuery;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay\WhoDayQueryResponse;

class WhoCommand extends AbstractCustomUserCommand
{
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        /** @var WhoDayQueryResponse $queryResponse */
        $queryResponse = $this->handle(
            new WhoDayQuery(
                $message->getChat()->getId(),
                $message->getFrom()->getId()
            )
        );

        return Request::sendMessage(
            $this->renderMessage(
                new WhoCommandResponseRenderRequest(
                    $message->getChat()->getId(),
                    $queryResponse->userPositions
                )
            )
        );
    }
}
