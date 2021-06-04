<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord;

interface UserMessageRecordRepositoryInterface
{
    public const BY_MINUTE = 'minute';
    public const BY_HOUR = 'hour';

    public function save(UserMessageRecord $record): void;

    /**
     * @return array<UserPosition>
     */
    public function findPositionsWithinHours(int $chatId, int $withinHours, int $topUsersCount): array;

    /**
     * @return array<UserMessageRecord>
     */
    public function getAllRecordsWithinHours(int $chatId, int $withinHours, int $offsetHours = 0): array;

    /**
     * @return array<MessagesAtTimeCount>
     */
    public function getMessagesAggregatedByTime(int $chatId, int $withinHours, int $offsetHours, string $interval): array;

    public function ensureMessagesOlderThanExist(int $chatId, int $olderThanHours): bool;

    /**
     * @return array<int>
     */
    public function getAliveChatsWithinHours(int $withinHours): array;
}
