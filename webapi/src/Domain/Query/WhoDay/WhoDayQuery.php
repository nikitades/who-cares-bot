<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class WhoDayQuery
{
    public function __construct(
        public int $chatId,
        public int $userId
    ) {
    }
}
