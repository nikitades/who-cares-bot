<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord;

interface UserMessageRecordRepositoryInterface
{
    public const BY_MINUTE = 'minute';
    public const BY_HOUR = 'hour';

    public function save(UserMessageRecord $record): void;

    /**
     * @return array<UserPosition>
     */
    public function findPositionsWithinDays(int $chatId, int $withinHours, int $topUsersCount): array;

    /**
     * @return array<UserMessageRecord>
     */
    public function getAllRecordsWithinDays(int $chatId, int $withinHours): array;

    /**
     * @return array<MessagesAtTimeCount>
     */
    public function getMessagesAggregatedByTime(int $chatId, int $withinHours, string $interval): array;

    /**
     * @return array<int>
     */
    public function getAliveChatsWithinDays(int $withinHours): array;
}
