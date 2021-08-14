<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UserPosition
{
    public function __construct(
        public string $userNickname,
        public int $userId,
        public int $userMessagesCount
    ) {
    }
}
