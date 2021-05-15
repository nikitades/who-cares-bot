<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay;

use JetBrains\PhpStorm\Immutable;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;

#[Immutable]
class WhoDayQueryResponse
{
    /**
     * @param array<UserPosition> $userPositions
     * @param string $imageContent
     */
    public function __construct(
        public array $userPositions,
        public string $imageContent
    ) {
    }
}
