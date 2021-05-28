<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Test\Unit\App\AsyncCommand\CalculateChatMedian;

use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\CalculateChatMedian\CalculateChatMedianCommand;
use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\CalculateChatMedian\CalculateChatMedianCommandHandler;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatMedian;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatMedianRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UuidProviderInterface;
use Nikitades\WhoCaresBot\WebApi\Infrastructure\Symfony\SymfonyUuidProvider;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;

class CalculateChatMedianCommandHandlerTest extends TestCase
{
    public function testMedianIsCalculatedCorrectly(): void
    {
        $newChatMedianId = (new SymfonyUuidProvider())->provide();

        $userMessageRecordRepository = $this->createMock(UserMessageRecordRepositoryInterface::class);
        $userMessageRecordRepository->expects(static::once())
            ->method('getMessagesAggregatedByTime')
            ->with(chatId: 13, withinHours: 30 * 24, interval: UserMessageRecordRepositoryInterface::BY_HOUR)
            ->willReturn([
                new MessagesAtTimeCount(
                    13,
                    24,
                    new DateTime('2021-01-01 05:00:00')
                ),
                new MessagesAtTimeCount(
                    13,
                    38,
                    new DateTime('2021-01-01 06:00:00')
                ),
                new MessagesAtTimeCount(
                    13,
                    96,
                    new DateTime('2021-01-01 07:00:00')
                ),
                new MessagesAtTimeCount(
                    13,
                    12,
                    new DateTime('2021-01-01 08:00:00')
                ),
            ]);

        $chatMedianRepository = $this->createMock(ChatMedianRepositoryInterface::class);
        $chatMedianRepository->expects(static::once())->method('save')
            ->willReturnCallback(function (ChatMedian $newChatMedian) use ($newChatMedianId): void {
                static::assertEquals($newChatMedianId, $newChatMedian->getId());
                static::assertEquals(13, $newChatMedian->getChatId());
                static::assertEquals(24, $newChatMedian->getMedian());
            });

        $uuidProvider = $this->createMock(UuidProviderInterface::class);
        $uuidProvider->expects(static::once())->method('provide')->willReturn($newChatMedianId);

        $calculateChatMedianCommandHandler = new CalculateChatMedianCommandHandler(
            $userMessageRecordRepository,
            $chatMedianRepository,
            $uuidProvider
        );

        $calculateChatMedianCommandHandler->__invoke(new CalculateChatMedianCommand(13));
    }
}
