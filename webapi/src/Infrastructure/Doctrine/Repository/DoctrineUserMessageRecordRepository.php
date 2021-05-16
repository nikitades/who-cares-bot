<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecord;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;

/**
 * @extends ServiceEntityRepository<UserMessageRecord>
 */
class DoctrineUserMessageRecordRepository extends ServiceEntityRepository implements UserMessageRecordRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMessageRecord::class);
    }

    public function save(UserMessageRecord $record): void
    {
        $this->getEntityManager()->persist($record);
    }
}
