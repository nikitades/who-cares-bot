<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GeneratePeakAnalysisReport;

use DateInterval;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class PeakAnalysisReport
{
    public function __construct(
        public int $messagesCount,
        public DateInterval $timeLength,
        public float $averageFrequencyPerMinute,
        public float $peakFrequencyPerMinute,
        public string $mostActivePersonName
    ) {
    }
}
