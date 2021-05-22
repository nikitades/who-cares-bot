<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\GenerateTop;

use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;

class GenerateTopCommandHandler implements CommandHandlerInterface
{
    private function __construct(private UserMessageRecordRepositoryInterface $userMessageRecordRepository)
    {
    }

    public function __invoke(GenerateTopCommand $command): void
    {
        $messageRecords = $this->userMessageRecordRepository->getAllRecordsWithinDays($command->chatId, $command->withinDays);

        Request::sendMessage([
            'chat_id' => $command->chatId,
            'text' => 'privet',
        ]);
    }
}
