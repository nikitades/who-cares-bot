<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\GenerateWhoReport;

use JetBrains\PhpStorm\Immutable;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\AsyncCommandInterface;

#[Immutable]
class GenerateWhoReportCommand implements AsyncCommandInterface
{
    public function __construct(
        public int $chatId,
        public int $userId,
        public int $withinDays
    ) {
    }
}
