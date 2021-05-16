<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Uid\Uuid;

/**
 * @Entity
 */
class UserMessageRecord
{
    /**
     * @Id
     * @Column(type="uuid", unique=true)
     */
    private Uuid $id;

    /**
     * @Column(type="integer")
     */
    private int $messageId;

    /**
     * @Column(type="integer", nullable=true)
     */
    private ?int $replyToMessageId;

    /**
     * @Column(type="integer")
     */
    private int $chatId;

    /**
     * @Column(type="integer")
     */
    private int $authorId;

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
}
