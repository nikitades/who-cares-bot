<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord;

use Safe\DateTime;

class MessagesAtTimeCount
{
    public function __construct(
        public int $chatId,
        public int $messagesCount,
        public DateTime $time
    ) {
    }
}
