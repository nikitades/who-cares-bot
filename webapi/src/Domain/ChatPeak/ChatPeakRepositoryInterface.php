<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\ChatPeak;

interface ChatPeakRepositoryInterface
{
    public function save(ChatPeak $peak): void;

    public function findByChatId(int $chatId): ?ChatPeak;
}
