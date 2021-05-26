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
    public function findPositionsWithinDays(int $chatId, int $withinDays, int $topUsersCount): array;

    /**
     * @return array<UserMessageRecord>
     */
    public function getAllRecordsWithinDays(int $chatId, int $withinDays): array;

    /**
     * @return array<MessagesAtTimeCount>
     */
    public function getMessagesAggregatedByTime(int $chatId, int $withinDays, int $secondsInterval): array;

    /**
     * @return array<int>
     */
    public function getAliveChatsWithinDays(int $withinDays): array;
}
