<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatPeak\ChatPeak;
use Nikitades\WhoCaresBot\WebApi\Domain\ChatPeak\ChatPeakRepositoryInterface;

/**
 * @extends ServiceEntityRepository<ChatPeak>
 */
class DoctrineChatPeakRepository extends ServiceEntityRepository implements ChatPeakRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatPeak::class);
    }

    public function findByChatId(int $chatId): ?ChatPeak
    {
        return $this->createQueryBuilder('m')
            ->where('m.chatId = :chatId')->setParameter('chatId', $chatId)
            ->setMaxResults(1)
            ->orderBy('m.createdAt', Criteria::DESC)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(ChatPeak $peak): void
    {
        $this->getEntityManager()->persist($peak);
        $this->getEntityManager()->flush();
    }
}
