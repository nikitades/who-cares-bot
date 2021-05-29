<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian;

interface ChatAverageRepositoryInterface
{
    public function save(ChatAverage $median): void;

    public function findByChatId(int $chatId): ?ChatAverage;
}
