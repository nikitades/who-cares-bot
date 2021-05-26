<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\CalculateChatMedian;

use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatMedian;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatMedianRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UuidProviderInterface;
use Safe\DateTime;

class CalculateChatMedianCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private ChatMedianRepositoryInterface $chatMedianRepository,
        private UuidProviderInterface $uuidProvider
    ) {
    }

    public function __invoke(CalculateChatMedianCommand $command): void
    {
        $chatMessagesApproximated = $this->userMessageRecordRepository->getMessagesAggregatedByTime(
            chatId: $command->chatId,
            withinDays: 30,
            secondsInterval: 15
        );

        if (0 === count($chatMessagesApproximated)) {
            return;
        }

        /** @var array<int> $plainMessagesCountArray */
        $plainMessagesCountArray = array_map(
            fn (MessagesAtTimeCount $record): int => $record->messagesCount,
            $chatMessagesApproximated
        );

        $medianValue = $plainMessagesCountArray[count($plainMessagesCountArray) - 1];

        $this->chatMedianRepository->save(
            new ChatMedian(
                id: $this->uuidProvider->provide(),
                chatId: $command->chatId,
                median: $medianValue,
                createdAt: new DateTime('now')
            )
        );
    }
}
