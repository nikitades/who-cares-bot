<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\CalculateChatAverage;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CalculateChatAverageCommand
{
    public function __construct(public int $chatId)
    {
    }
}
