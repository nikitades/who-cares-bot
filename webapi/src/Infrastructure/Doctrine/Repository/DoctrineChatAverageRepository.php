<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatAverage;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian\ChatAverageRepositoryInterface;

/**
 * @extends ServiceEntityRepository<ChatAverage>
 */
class DoctrineChatAverageRepository extends ServiceEntityRepository implements ChatAverageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatAverage::class);
    }

    public function findByChatId(int $chatId): ?ChatAverage
    {
        return $this->createQueryBuilder('m')
            ->where('m.chatId = :chatId')->setParameter('chatId', $chatId)
            ->setMaxResults(1)
            ->orderBy('m.createdAt', Criteria::DESC)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(ChatAverage $median): void
    {
        $this->getEntityManager()->persist($median);
        $this->getEntityManager()->flush();
    }
}
