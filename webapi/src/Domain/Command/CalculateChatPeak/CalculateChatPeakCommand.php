<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\CalculateChatPeak;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CalculateChatPeakCommand
{
    public function __construct(public int $chatId)
    {
    }
}
