<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\DetectPeak;

use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\PeakDetectionResponseGenerator;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\ChatPeak\ChatPeakRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserMessageRecordRepositoryInterface;

class DetectPeakCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private ChatPeakRepositoryInterface $chatPeakRepository,
        private PeakDetectionResponseGenerator $peakDetectionResponseGenerator,
        private int $peakSearchPeriod
    ) {
    }

    public function __invoke(DetectPeakCommand $command): void
    {
        $chatPeak = $this->chatPeakRepository->findLastByChatId($command->chatId);

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
            offsetHours: 0,
            interval: UserMessageRecordRepositoryInterface::BY_HOUR
        );

        $messagesCount = array_reduce(
            $lastHourMessages,
            fn (int $carry, MessagesAtTimeCount $messagesCountRecord): int => $messagesCountRecord->messagesCount + $carry,
            0
        );

        $peakValue = floor($messagesCount / $chatPeak->getPeak() * 8); //we take the peak value as 10th level of activity, but stimulate the chat to be more active by using only 8 as multiplier

        $this->peakDetectionResponseGenerator->process($command->chatId, (int) $peakValue);
    }
}
