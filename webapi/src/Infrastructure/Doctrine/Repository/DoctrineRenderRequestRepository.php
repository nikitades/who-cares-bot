<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\RenderRequest\RenderRequest;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\RenderRequest\RenderRequestRepositoryInterface;

/**
 * @extends ServiceEntityRepository<RenderRequest>
 */
class DoctrineRenderRequestRepository extends ServiceEntityRepository implements RenderRequestRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RenderRequest::class);
    }

    public function saveOrUpdate(RenderRequest $renderRequest): void
    {
        $oldEntity = $this->findById($renderRequest->getKey());
        if (null !== $oldEntity) {
            $oldEntity->setData($renderRequest->getData());
            $this->getEntityManager()->flush();

            return;
        }

        $this->getEntityManager()->persist($renderRequest);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?RenderRequest
    {
        return parent::find($id);
    }

    public function delete(string $key): void
    {
        $this->createQueryBuilder('rr')
            ->delete()
            ->where('rr.key = :key')->setParameter('key', $key)
            ->getQuery()
            ->execute();
    }
}
