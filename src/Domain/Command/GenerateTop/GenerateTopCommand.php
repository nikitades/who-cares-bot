<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\GenerateTop;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class GenerateTopCommand
{
    public function __construct(
        public int $withinDays,
        public int $topUsersCount,
        public int $chatId
    ) {
    }
}
