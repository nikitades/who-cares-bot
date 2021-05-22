<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who;

use JetBrains\PhpStorm\Immutable;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;

#[Immutable]
final class WhoCommandResponseRenderRequest
{
    /**
     * @param array<UserPosition> $userPositions
     */
    public function __construct(
        public int $chatId,
        public array $userPositions
    ) {
    }
}
