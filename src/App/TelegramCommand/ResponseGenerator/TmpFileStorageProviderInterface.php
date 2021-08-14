<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator;

interface TmpFileStorageProviderInterface
{
    public function storeFileTemporarily(string $fileContents): string;
}
