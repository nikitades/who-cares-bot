<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\GenerateWho;

use JetBrains\PhpStorm\Immutable;
use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\AsyncCommandInterface;

#[Immutable]
class GenerateWhoCommand implements AsyncCommandInterface
{
    public function __construct(
        public int $chatId,
        public int $userId,
        public int $daysAmount
    ) {
    }
}
