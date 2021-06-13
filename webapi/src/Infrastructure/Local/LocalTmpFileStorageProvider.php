<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Local;

use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\TmpFileStorageProviderInterface;
use function Safe\file_put_contents;
use function Safe\tempnam;

class LocalTmpFileStorageProvider implements TmpFileStorageProviderInterface
{
    public function storeFileTemporarily(string $fileContents): string
    {
        $tmpFilePath = tempnam('/tmp', 'nkitades_whocaresbot');
        file_put_contents($tmpFilePath, $fileContents);

        return $tmpFilePath;
    }
}
