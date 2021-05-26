<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\CalculateChatMedian;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CalculateChatMedianCommand
{
    public function __construct(public int $chatId)
    {
    }
}
