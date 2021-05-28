<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian;

interface ChatMedianRepositoryInterface
{
    public function save(ChatMedian $median): void;

    public function findByChatId(int $chatId): ?ChatMedian;
}
