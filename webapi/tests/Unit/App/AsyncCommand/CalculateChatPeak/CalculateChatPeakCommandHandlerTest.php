<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Test\Unit\Domain\Command\CalculateChatPeak;

use Nikitades\WhoCaresBot\WebApi\Domain\Command\CalculateChatPeak\CalculateChatPeakCommand;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CalculateChatPeak\CalculateChatPeakCommandHandler;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\ChatPeak\ChatPeak;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\ChatPeak\ChatPeakRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UuidProviderInterface;
use Nikitades\WhoCaresBot\WebApi\Infrastructure\Symfony\SymfonyUuidProvider;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;

class CalculateChatPeakCommandHandlerTest extends TestCase
{
    public function testPeakIsCalculatedCorrectly(): void
    {
        $newChatPeakId = (new SymfonyUuidProvider())->provide();

        $userMessageRecordRepository = $this->createMock(UserMessageRecordRepositoryInterface::class);
        $userMessageRecordRepository->expects(static::once())
            ->method('getMessagesAggregatedByTime')
            ->with(chatId: 13, withinHours: 720, exceptHours: 24, interval: UserMessageRecordRepositoryInterface::BY_HOUR)
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

        $chatPeakRepository = $this->createMock(ChatPeakRepositoryInterface::class);
        $chatPeakRepository->expects(static::once())->method('save')
            ->willReturnCallback(function (ChatPeak $newChatPeak) use ($newChatPeakId): void {
                static::assertEquals($newChatPeakId, $newChatPeak->getId());
                static::assertEquals(13, $newChatPeak->getChatId());
                static::assertEquals(96, $newChatPeak->getPeak());
            });

        $uuidProvider = $this->createMock(UuidProviderInterface::class);
        $uuidProvider->expects(static::once())->method('provide')->willReturn($newChatPeakId);

        $calculateChatPeakCommandHandler = new CalculateChatPeakCommandHandler(
            $userMessageRecordRepository,
            $chatPeakRepository,
            $uuidProvider,
            720
        );

        $calculateChatPeakCommandHandler->__invoke(new CalculateChatPeakCommand(13));
    }
}
