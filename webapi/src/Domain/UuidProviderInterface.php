<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain;

use Symfony\Component\Uid\Uuid;

interface UuidProviderInterface
{
    public function provide(): Uuid;
}
