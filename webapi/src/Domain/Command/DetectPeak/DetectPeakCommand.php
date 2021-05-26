<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\DetectPeak;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class DetectPeakCommand
{
    public function __construct(public int $chatId)
    {
    }
}
