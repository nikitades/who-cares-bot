<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\StartThrottledPeakDetection;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class StartThrottledPeakDetectionCommand
{
    public function __construct(public int $chatId)
    {
    }
}
