<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomUserCommand;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\Who\WhoQuery;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\Who\WhoQueryResponse;

class WhoWeekCommand extends AbstractCustomUserCommand
{
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        /** @var WhoQueryResponse $queryResponse */
        $queryResponse = $this->handle(
            new WhoQuery(
                $message->getChat()->getId(),
                $message->getFrom()->getId(),
                7
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
