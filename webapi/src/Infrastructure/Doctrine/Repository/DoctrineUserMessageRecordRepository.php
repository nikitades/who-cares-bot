<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Repository;

use DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
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

    public function findPositionsWithinDays(int $daysAmount, int $topUsersCount): array
    {
        $dateFrom = (new DateTime('midnight'))->sub(new DateInterval(sprintf('P%sD', $daysAmount - 1)));
        //TODO: придумать как добавить в агрегатную функцию имя и айди чуваков
        $data = $this->createQueryBuilder('r')
            ->select(['COUNT(r.id) as totalCount'])
            ->where('r.createdAt > :dateFrom')->setParameter('dateFrom', $dateFrom)
            ->groupBy('r.userId')
            ->setMaxResults($topUsersCount)
            ->getQuery()
            ->getScalarResult();

        return [];
    }

    public function save(UserMessageRecord $record): void
    {
        $this->getEntityManager()->persist($record);
    }
}
