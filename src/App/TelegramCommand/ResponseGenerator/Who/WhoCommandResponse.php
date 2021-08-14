<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\Who;

use JetBrains\PhpStorm\Immutable;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserPosition;

#[Immutable]
class WhoCommandResponse
{
    /**
     * @param array<UserPosition> $userPositions
     */
    public function __construct(
        public array $userPositions,
        public int $chatId,
        public string $imageContent
    ) {
    }
}
