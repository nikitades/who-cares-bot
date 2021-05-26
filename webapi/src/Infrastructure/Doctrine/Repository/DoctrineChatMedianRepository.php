<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatMedian;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatMedianRepositoryInterface;

/**
 * @extends ServiceEntityRepository<ChatMedian>
 */
class DoctrineChatMedianRepository extends ServiceEntityRepository implements ChatMedianRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatMedian::class);
    }

    public function save(ChatMedian $median): void
    {
        $this->getEntityManager()->persist($median);
        $this->getEntityManager()->flush();
    }
}
