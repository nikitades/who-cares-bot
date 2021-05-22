<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GenerateTop;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class GenerateTopCommand
{
    public function __construct(
        public int $withinDays,
        public int $chatId
    ) {
    }
}
