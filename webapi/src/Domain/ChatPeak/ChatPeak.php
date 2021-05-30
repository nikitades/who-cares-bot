<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\ChatPeak;

use DateTimeInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Uid\Uuid;

/**
 * @Entity
 */
class ChatPeak
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
    private int $peak;

    /**
     * @Column(type="datetime")
     */
    private DateTimeInterface $peakDate;

    /**
     * @Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    public function __construct(
        Uuid $id,
        int $chatId,
        int $peak,
        DateTimeInterface $peakDate,
        DateTimeInterface $createdAt
    ) {
        $this->id = $id;
        $this->chatId = $chatId;
        $this->peak = $peak;
        $this->peakDate = $peakDate;
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

    public function getPeak(): int
    {
        return $this->peak;
    }

    public function getPeakDate(): DateTimeInterface
    {
        return $this->peakDate;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
