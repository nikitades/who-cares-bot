<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\GeneratePeakAnalysisReport;

use JetBrains\PhpStorm\Immutable;
use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\AsyncCommandInterface;

#[Immutable]
class GeneratePeakAnalysisReportCommand implements AsyncCommandInterface
{
    public function __construct(
        public int $chatId,
        public int $withinDays
    ) {
    }
}
