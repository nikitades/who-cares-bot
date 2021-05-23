<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\RenderRequest;

interface RenderRequestRepositoryInterface
{
    public function saveOrUpdate(RenderRequest $renderRequest): void;

    public function findById(string $id): ?RenderRequest;
}
