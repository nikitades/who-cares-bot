<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Entity\ChatPeak;

interface ChatPeakRepositoryInterface
{
    public function save(ChatPeak $peak): void;

    public function findLastByChatId(int $chatId): ?ChatPeak;
}
