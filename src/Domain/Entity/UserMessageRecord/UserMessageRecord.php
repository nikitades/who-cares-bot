<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @Entity
 * @Table(
 *     indexes={
 *         @Index(
 *             name="created_at_chat_id_idx", columns={"created_at", "chat_id"}
 *         )
 *     }
 * )
 */
class UserMessageRecord
{
    /**
     * @Id
     * @Column(type="uuid", unique=true)
     */
    private Uuid $id;

    /**
     * @Column(type="bigint")
     */
    private int $messageId;

    /**
     * @Column(type="bigint", nullable=true)
     */
    private ?int $replyToMessageId;

    /**
     * @Column(type="bigint")
     */
    private int $chatId;

    /**
     * @Column(type="bigint")
     */
    private int $userId;

    /**
     * @Column(type="string")
     */
    private string $userNickname;

    /**
     * @Column(type="datetime")
     */
    private DateTimeInterface $messageTime;

    /**
     * @Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @Column(type="text", nullable=true)
     */
    private ?string $text;

    /**
     * @Column(type="integer")
     */
    private int $textLength;

    /**
     * @Column(type="text", nullable=true)
     */
    private ?string $stickerId;

    /**
     * @Column(type="text")
     */
    private string $attachType;

    public function __construct(
        Uuid $id,
        int $messageId,
        ?int $replyToMessageId,
        int $chatId,
        int $userId,
        string $userNickname,
        DateTimeInterface $messageTime,
        DateTimeInterface $createdAt,
        ?string $text,
        int $textLength,
        string $attachType,
        ?string $stickerId
    ) {
        $this->id = $id;
        $this->messageId = $messageId;
        $this->replyToMessageId = $replyToMessageId;
        $this->chatId = $chatId;
        $this->userNickname = $userNickname;
        $this->userId = $userId;
        $this->messageTime = $messageTime;
        $this->createdAt = $createdAt;
        $this->text = $text;
        $this->textLength = $textLength;
        $this->attachType = $attachType;
        $this->stickerId = $stickerId;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUserNickname(): string
    {
        return $this->userNickname;
    }
}
