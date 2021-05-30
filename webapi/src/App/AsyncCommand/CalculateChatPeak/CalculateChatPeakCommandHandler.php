<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\CalculateChatPeak;

use Nikitades\WhoCaresBot\WebApi\Domain\ChatPeak\ChatPeak;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatPeak\ChatPeakRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;

use Nikitades\WhoCaresBot\WebApi\Domain\UuidProviderInterface;
use Safe\DateTime;

class CalculateChatPeakCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private ChatPeakRepositoryInterface $chatPeakRepository,
        private UuidProviderInterface $uuidProvider,
        private int $peakSearchPeriod
    ) {
    }

    public function __invoke(CalculateChatPeakCommand $command): void
    {
        $chatMessagesApproximated = $this->userMessageRecordRepository->getMessagesAggregatedByTime(
            chatId: $command->chatId,
            withinHours: $this->peakSearchPeriod,
            interval: UserMessageRecordRepositoryInterface::BY_HOUR
        );

        $peak = $this->calculateChatMessagesPeak($chatMessagesApproximated);

        if (null === $peak) {
            return;
        }

        //TODO: extend chat peak, add message at that time ID
        $this->chatPeakRepository->save(
            new ChatPeak(
                id: $this->uuidProvider->provide(),
                chatId: $command->chatId,
                peak: $peak->messagesCount,
                peakDate: $peak->time,
                createdAt: new DateTime('now')
            )
        );
    }

    /**
     * @param array<MessagesAtTimeCount> $chatMessagesApproximated
     */
    private function calculateChatMessagesPeak(array $chatMessagesApproximated): ?MessagesAtTimeCount
    {
        /** @var MessagesAtTimeCount|null $return */
        $return = null;

        foreach ($chatMessagesApproximated as $messagesAtTimeCount) {
            if ($return?->messagesCount < $messagesAtTimeCount->messagesCount) {
                $return = $messagesAtTimeCount;
            }
        }

        return $return;
    }
}
