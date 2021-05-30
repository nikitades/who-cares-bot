<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\DetectPeak;

use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response\PeakDetectionResponse;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatPeak\ChatPeakRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;

class DetectPeakCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private ChatPeakRepositoryInterface $chatPeakRepository,
        private int $peakSearchPeriod
    ) {
    }

    public function __invoke(DetectPeakCommand $command): void
    {
        $chatPeak = $this->chatPeakRepository->findByChatId($command->chatId);

        if (null === $chatPeak) {
            return;
        }

        if (!$this->userMessageRecordRepository->ensureMessagesOlderThanExist(
            chatId: $command->chatId,
            olderThanHours: $this->peakSearchPeriod
        )) {
            return;
        }

        $lastHourMessages = $this->userMessageRecordRepository->getMessagesAggregatedByTime(
            chatId: $command->chatId,
            withinHours: 1,
            exceptHours: 0,
            interval: UserMessageRecordRepositoryInterface::BY_HOUR
        );

        $messagesCount = array_reduce(
            $lastHourMessages,
            fn (int $carry, MessagesAtTimeCount $messagesCountRecord): int => $messagesCountRecord->messagesCount + $carry,
            0
        );

        $peakValue = floor($messagesCount / $chatPeak->getPeak() * 8); //we take the peak value as 10th level of activity, but stimulate the chat to be more active by using only 8 as multiplier

        (new PeakDetectionResponse($command->chatId, (int) $peakValue))->process();
    }
}
