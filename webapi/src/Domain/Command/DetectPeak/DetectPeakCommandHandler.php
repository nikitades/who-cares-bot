<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\DetectPeak;

use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response\PeakDetectionResponse;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatAverageRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;

class DetectPeakCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private ChatAverageRepositoryInterface $chatAverageRepository
    ) {
    }

    public function __invoke(DetectPeakCommand $command): void
    {
        $chatAverage = $this->chatAverageRepository->findByChatId($command->chatId);

        if (null === $chatAverage) {
            return;
        }

        $lastHourMessages = $this->userMessageRecordRepository->getMessagesAggregatedByTime(
            chatId: $command->chatId,
            withinHours: 1,
            interval: UserMessageRecordRepositoryInterface::BY_HOUR
        );

        $messagesCount = array_reduce(
            $lastHourMessages,
            fn (int $carry, MessagesAtTimeCount $messagesCountRecord): int => $messagesCountRecord->messagesCount + $carry,
            0
        );

        $peakValue = round($messagesCount / $chatAverage->getAverage()); //we take the average value as 1st level of activity

        (new PeakDetectionResponse($command->chatId, (int) $peakValue))->process();
    }
}
