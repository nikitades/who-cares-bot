<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Symfony;

use Nikitades\WhoCaresBot\WebApi\Domain\UuidProviderInterface;
use Symfony\Component\Uid\Uuid;

class SymfonyUuidProvider implements UuidProviderInterface
{
    public function provide(): Uuid
    {
        return Uuid::v4();
    }
}
