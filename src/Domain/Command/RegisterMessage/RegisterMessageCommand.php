<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\RegisterMessage;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class RegisterMessageCommand
{
    public function __construct(
        public int $messageId,
        public ?int $replyToMessageId,
        public int $userId,
        public string $userNickname,
        public int $chatId,
        public int $timestamp,
        public ?string $text,
        public string $attachType,
        public ?string $stickedId = null
    ) {
    }
}
