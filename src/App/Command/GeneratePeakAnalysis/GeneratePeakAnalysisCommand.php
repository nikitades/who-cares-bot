<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GeneratePeakAnalysis;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class GeneratePeakAnalysisCommand
{
    public function __construct(
        public int $chatId,
        public int $withinDays
    ) {
    }
}
