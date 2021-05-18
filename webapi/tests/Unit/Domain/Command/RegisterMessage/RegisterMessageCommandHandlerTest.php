<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Test\Unit\Domain\Command\RegisterMessage;

use Nikitades\WhoCaresBot\WebApi\Domain\Command\RegisterMessage\RegisterMessageCommand;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\RegisterMessage\RegisterMessageCommandHandler;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Infrastructure\Symfony\SymfonyUuidProvider;
use PHPUnit\Framework\TestCase;

class RegisterMessageCommandHandlerTest extends TestCase
{
    /**
     * @dataProvider getTestData
     */
    public function testRecordIsSaved(
        int $messageId,
        int $replyToMessageId,
        int $chatId,
        int $userId,
        string $userNickname,
        int $timestamp,
        string $text,
        string $attachType,
        string $stickerId
    ): void {
        $userRecordRepository = $this->createMock(UserMessageRecordRepositoryInterface::class);
        $userRecordRepository->expects(static::once())->method('save');

        $registerMessageCommandHandler = new RegisterMessageCommandHandler(
            $userRecordRepository,
            new SymfonyUuidProvider()
        );
        $registerMessageCommandHandler(
            new RegisterMessageCommand(
                messageId: $messageId,
                replyToMessageId: $replyToMessageId,
                chatId: $chatId,
                userId: $userId,
                userNickname: $userNickname,
                timestamp: $timestamp,
                text: $text,
                attachType: $attachType,
                stickedId: $stickerId
            )
        );
    }

    /**
     * @return array<array<string,string|int>>
     */
    public function getTestData(): array
    {
        return [
            [
                'messageId' => 123,
                'replyToMessageId' => 321,
                'chatId' => 222,
                'userId' => 111,
                'userNickname' => 'amogus',
                'timestamp' => time(),
                'text' => 'some text',
                'attachType' => 'text',
                'stickerId' => 'someStickerId',
            ],
        ];
    }
}
