<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord;

use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;

interface UserMessageRecordRepositoryInterface
{
    public function save(UserMessageRecord $record): void;

    /**
     * @return array<UserPosition>
     */
    public function findPositionsWithinDays(int $chatId, int $daysAmount, int $topUsersCount): array;
}
