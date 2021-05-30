<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\GenerateActivityReport;

use JetBrains\PhpStorm\Immutable;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\AsyncCommandInterface;

#[Immutable]
class GenerateActivityReportCommand implements AsyncCommandInterface
{
    public function __construct(
        public int $chatId,
        public int $userId,
        public int $withinDays
    ) {
    }
}
