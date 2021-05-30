<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Repository;

use DateInterval;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecord;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserPosition;

use Safe\DateTime;

use function Safe\sprintf;

/**
 * @extends ServiceEntityRepository<UserMessageRecord>
 */
class DoctrineUserMessageRecordRepository extends ServiceEntityRepository implements UserMessageRecordRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMessageRecord::class);
    }

    public function getAliveChatsWithinDays(int $withinHours): array
    {
        return array_column(
            $this->createQueryBuilder('r')
                ->select('r.chatId')
                ->where('r.createdAt > :dateFrom')->setParameter('dateFrom', $this->getDateFrom($withinHours))
                ->groupBy('r.chatId')
                ->getQuery()
                ->getScalarResult(),
            'chatId'
        );
    }

    /**
     * @return array<MessagesAtTimeCount>
     */
    public function getMessagesAggregatedByTime(int $chatId, int $withinHours, string $interval): array
    {
        if (!in_array(
            $interval,
            [UserMessageRecordRepositoryInterface::BY_HOUR, UserMessageRecordRepositoryInterface::BY_MINUTE],
            true
        )) {
            throw new LogicException(sprintf('Unknown interval %s', $interval));
        }

        $result = $this->createQueryBuilder('r')
            ->select('COUNT(r.id) as messagesCount, DATE_TRUNC(:interval, r.createdAt) as time')->setParameter('interval', sprintf('%s', $interval))
            ->where('r.chatId = :chatId')->setParameter('chatId', $chatId)
            ->andWhere('r.createdAt > :dateFrom')->setParameter('dateFrom', $this->getDateFrom($withinHours))
            ->groupBy('time')
            ->orderBy('time', Criteria::ASC)
            ->getQuery()
            ->getScalarResult();

        return array_map(
            fn (array $array): MessagesAtTimeCount => new MessagesAtTimeCount(
                chatId: $chatId,
                messagesCount: $array['messagesCount'],
                time: new DateTime($array['time'])
            ),
            $result
        );
    }

    /**
     * {@inheritDoc}
     */
    public function ensureMessagesOlderThanExist(int $chatId, int $olderThanHours): bool
    {
        return [] !== $this->createQueryBuilder('r')
            ->select('r.id')
            ->where('r.createdAt < :dateTo')->setParameter('dateTo', $this->getDateFrom($olderThanHours))
            ->setMaxResults(1)
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * {@inheritDoc}
     */
    public function findPositionsWithinDays(int $chatId, int $withinHours, int $topUsersCount): array
    {
        $result = $this->createQueryBuilder('r')
            ->select('COUNT(r.id) as totalCount, r.userId, r.userNickname')
            ->where('r.createdAt > :dateFrom')->setParameter('dateFrom', $this->getDateFrom($withinHours))
            ->andWhere('r.chatId = :chatId')->setParameter('chatId', $chatId)
            ->orderBy('totalCount', Criteria::DESC)
            ->groupBy('r.userId, r.userNickname')
            ->setMaxResults($topUsersCount)
            ->getQuery()
            ->getScalarResult();

        return array_map(
            fn (array $row): UserPosition => new UserPosition(
                $row['userNickname'],
                $row['userId'],
                $row['totalCount']
            ),
            $result
        );
    }

    /**
     * @return array<UserMessageRecord>
     */
    public function getAllRecordsWithinDays(int $chatId, int $withinHours): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.createdAt > :dateFrom')->setParameter('dateFrom', $this->getDateFrom($withinHours))
            ->andWhere('r.chatId = :chatId')->setParameter('chatId', $chatId)
            ->orderBy('r.createdAt', Criteria::ASC)
            ->getQuery()
            ->getResult();
    }

    public function save(UserMessageRecord $record): void
    {
        $this->getEntityManager()->persist($record);
        $this->getEntityManager()->flush();
    }

    private function getDateFrom(int $withinHours): DateTimeInterface
    {
        return (new DateTime('now'))->sub(new DateInterval(sprintf('PT%sH', $withinHours)));
    }
}
