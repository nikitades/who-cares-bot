<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand;

interface RenderedPageProviderInterface
{
    /**
     * @param array<string> $labels
     * @param array<int> $positions
     */
    public function getRegularTopImage(array $labels, array $positions): string;
}