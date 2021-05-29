<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\CalculateChatAverage;

use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatAverage;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatAverageRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UuidProviderInterface;
use Safe\DateTime;

use function Safe\sort;

class CalculateChatAverageCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private ChatAverageRepositoryInterface $chatMedianRepository,
        private UuidProviderInterface $uuidProvider
    ) {
    }

    public function __invoke(CalculateChatAverageCommand $command): void
    {
        $chatMessagesApproximated = $this->userMessageRecordRepository->getMessagesAggregatedByTime(
            chatId: $command->chatId,
            withinHours: 24,
            interval: UserMessageRecordRepositoryInterface::BY_HOUR
        );

        $this->chatMedianRepository->save(
            new ChatAverage(
                id: $this->uuidProvider->provide(),
                chatId: $command->chatId,
                average: $this->calculateChatMessagesMedianFrequency($chatMessagesApproximated),
                createdAt: new DateTime('now')
            )
        );
    }

    /**
     * @param array<MessagesAtTimeCount> $chatMessagesApproximated
     */
    private function calculateChatMessagesMedianFrequency(array $chatMessagesApproximated): int
    {
        if (0 === count($chatMessagesApproximated)) {
            return 0;
        }

        if (1 === count($chatMessagesApproximated)) {
            return $chatMessagesApproximated[0]->messagesCount;
        }

        $chatMessagesApproximated = array_filter(
            $chatMessagesApproximated,
            fn (MessagesAtTimeCount $record): bool => $record->messagesCount > 0
        );

        /** @var array<int> $plainMessagesCountArray */
        $plainMessagesCountArray = array_map(
            fn (MessagesAtTimeCount $record): int => $record->messagesCount,
            $chatMessagesApproximated
        );

        sort($plainMessagesCountArray);

        $medianIndex = (int) round(count($plainMessagesCountArray) / 2) - 1;

        return $plainMessagesCountArray[$medianIndex];
    }
}
