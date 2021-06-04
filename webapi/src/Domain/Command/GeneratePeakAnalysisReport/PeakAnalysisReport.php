<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\GeneratePeakAnalysisReport;

use DateInterval;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class PeakAnalysisReport
{
    public function __construct(
        public int $messagesCount,
        public DateInterval $timeLength,
        public int $averageFrequencyPerMinute,
        public string $mostActivePersonName
    ) {
    }
}
