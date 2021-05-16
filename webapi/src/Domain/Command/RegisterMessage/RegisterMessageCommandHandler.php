<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\RegisterMessage;

use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecord;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UuidProviderInterface;

class RegisterMessageCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private UuidProviderInterface $uuidProvider
    ) {
    }

    public function __invoke(RegisterMessageCommand $command): void
    {
        $this->userMessageRecordRepository->save(
            new UserMessageRecord(
                id: $this->uuidProvider->provide(),
                messageId: $command->messageId,
                replyToMessageId: $command->replyToMessageId,
                chatId: $command->chatId,
                authorId: $command->userId,
                text: $command->text,
                textLength: mb_strlen($command->text ?? ''),
                attachType: $command->attachType,
                stickerId: $command->stickedId
            )
        );
    }
}
