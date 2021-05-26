<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Repository;

use DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecord;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
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

    public function getAliveChatsWithinDays(int $withinDays): array
    {
        $dateFrom = (new DateTime('midnight'))->sub(new DateInterval(sprintf('P%sDT3H', $withinDays - 1)));

        return array_column(
            $this->createQueryBuilder('r')
                ->select('r.chatId')
                ->where('r.createdAt > :dateFrom')->setParameter('dateFrom', $dateFrom)
                ->groupBy('r.chatId')
                ->getQuery()
                ->getScalarResult(),
            'chatId'
        );
    }

    /**
     * @return array<MessagesAtTimeCount>
     */
    public function getMessagesAggregatedByTime(int $chatId, int $withinDays, int $secondsInterval): array
    {
        $dateFrom = (new DateTime('midnight'))->sub(new DateInterval(sprintf('P%sDT3H', $withinDays - 1)));

        $result = $this->createQueryBuilder('r')
            ->select('COUNT(r.id) as messagesCount, DATE_TRUNC(\'hour\', r.createdAt) time')
            ->where('r.chatId = :chatId')->setParameter('chatId', $chatId)
            ->andWhere('r.createdAt > :dateFrom')->setParameter('dateFrom', $dateFrom)
            ->groupBy('time')
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
    public function findPositionsWithinDays(int $chatId, int $withinDays, int $topUsersCount): array
    {
        $dateFrom = (new DateTime('midnight'))->sub(new DateInterval(sprintf('P%sDT3H', $withinDays - 1)));

        $result = $this->createQueryBuilder('r')
            ->select('COUNT(r.id) as totalCount, r.userId, r.userNickname')
            ->where('r.createdAt > :dateFrom')->setParameter('dateFrom', $dateFrom)
            ->andWhere('r.chatId = :chatId')->setParameter('chatId', $chatId)
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
    public function getAllRecordsWithinDays(int $chatId, int $withinDays): array
    {
        $dateFrom = (new DateTime('midnight'))->sub(new DateInterval(sprintf('P%sD', $withinDays - 1)));

        return $this->createQueryBuilder('r')
            ->where('r.createdAt > :dateFrom')->setParameter('dateFrom', $dateFrom)
            ->andWhere('r.chatId = :chatId')->setParameter('chatId', $chatId)
            ->getQuery()
            ->getResult();
    }

    public function save(UserMessageRecord $record): void
    {
        $this->getEntityManager()->persist($record);
        $this->getEntityManager()->flush();
    }
}
