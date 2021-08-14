<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\Activity;

use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\ResponseGeneratorInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\TmpFileStorageProviderInterface;
use function Safe\sprintf;

class ActivityResponseGenerator implements ResponseGeneratorInterface
{
    public function __construct(
        private TmpFileStorageProviderInterface $tmpFileStorageProvider
    ) {
    }

    public function process(ActivityResponse $activityResposne): void
    {
        Request::sendPhoto([
            'chat_id' => $activityResposne->chatId,
            'parse_mode' => 'Markdown',
            'caption' => sprintf('*Peak value: %s*', $activityResposne->peakValue),
            'photo' => Request::encodeFile($this->tmpFileStorageProvider->storeFileTemporarily($activityResposne->imageContent)),
        ]);
    }
}
