<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App;

interface RenderedPageProviderInterface
{
    /**
     * @param array<string> $labels
     * @param array<int> $positions
     */
    public function getRegularTopImage(array $labels, array $positions): string;

    /**
     * @param array<string> $labels
     * @param array<int> $positions
     * @return string
     */
    public function getActivityImage(array $labels, array $positions): string;
}
