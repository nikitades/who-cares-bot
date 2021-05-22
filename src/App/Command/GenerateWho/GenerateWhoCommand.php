<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GenerateWho;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class GenerateWhoCommand
{
    public function __construct(
        public int $chatId,
        public int $userId,
        public int $daysAmount
    ) {
    }
}
