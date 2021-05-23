<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command;

interface RenderedPageProviderInterface
{
    /**
     * @param array<string> $titles
     * @param array<int> $positions
     */
    public function getRegularTopImage(array $titles, array $positions): string;
}
