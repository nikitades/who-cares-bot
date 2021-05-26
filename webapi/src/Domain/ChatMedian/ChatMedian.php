<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\ChatMedian;

use DateTimeInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Uid\Uuid;

/**
 * @Entity
 */
class ChatMedian
{
    /**
     * @Id
     * @Column(type="uuid")
     */
    private Uuid $id;

    /**
     * @Column(type="integer")
     */
    private int $chatId;

    /**
     * @Column(type="integer")
     */
    private int $median;

    /**
     * @Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    public function __construct(
        Uuid $id,
        int $chatId,
        int $median,
        DateTimeInterface $createdAt
    ) {
        $this->id = $id;
        $this->chatId = $chatId;
        $this->median = $median;
        $this->createdAt = $createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }

    public function getMedian(): int
    {
        return $this->median;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
