<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GenerateWhoReport;

use JetBrains\PhpStorm\Immutable;
use Nikitades\WhoCaresBot\WebApi\App\Command\AsyncCommandInterface;

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
