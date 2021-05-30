<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\GeneratePeakAnalysisReport;

use JetBrains\PhpStorm\Immutable;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\AsyncCommandInterface;

#[Immutable]
class GeneratePeakAnalysisReportCommand implements AsyncCommandInterface
{
    public function __construct(
        public int $chatId,
        public int $withinDays
    ) {
    }
}
