<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Test\Domain\Command\RegisterMessage;

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
        int $authorId,
        int $chatId,
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
        $registerMessageCommandHandler(new RegisterMessageCommand(
            $messageId,
            $replyToMessageId,
            $authorId,
            $chatId,
            $text,
            $attachType,
            $stickerId
        ));
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
                'authorId' => 111,
                'chatId' => 222,
                'text' => 'some text',
                'attachType' => 'text',
                'stickerId' => 'someStickerId',
            ],
        ];
    }
}
