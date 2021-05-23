<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\RenderRequest;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

/**
 * @Entity
 */
class RenderRequest
{
    /**
     * @Id
     * @Column(type="text", unique=true)
     */
    private string $key;

    /**
     * @var array<string,mixed>
     * @Column(type="json")
     */
    private array $data;

    /**
     * @param array<string,mixed> $data
     */
    public function __construct(
        string $key,
        array $data
    ) {
        $this->key = $key;
        $this->data = $data;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array<string,mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
