<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\Activity;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class ActivityResponse
{
    public function __construct(
        public int $chatId,
        public int $peakValue,
        public string $imageContent
    ) {
    }
}
