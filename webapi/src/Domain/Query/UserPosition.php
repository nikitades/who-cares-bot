<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Query;

class UserPosition
{
    public function __construct(
        public string $userNickname,
        public int $userId,
        public int $userMessagesCount
    ) {
    }
}
