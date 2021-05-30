<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\CalculateChatPeak;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CalculateChatPeakCommand
{
    public function __construct(public int $chatId)
    {
    }
}
