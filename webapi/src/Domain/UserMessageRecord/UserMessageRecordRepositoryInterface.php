<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord;

interface UserMessageRecordRepositoryInterface
{
    public function save(UserMessageRecord $record): void;
}
