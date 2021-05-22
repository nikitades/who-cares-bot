<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GenerateWho;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who\WhoCommandResponse;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;

class GenerateWhoCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository
    ) {
    }

    public function __invoke(GenerateWhoCommand $command): ServerResponse
    {
        $userPositions = $this->userMessageRecordRepository->findPositionsWithinDays($command->chatId, $command->daysAmount, 4);

        $whoResponse = new WhoCommandResponse(
            $userPositions,
            $command->chatId
        );

        return Request::sendMessage($whoResponse->toArray());
    }
}
