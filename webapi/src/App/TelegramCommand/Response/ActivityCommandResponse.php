<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response;

use Longman\TelegramBot\Request;
use function Safe\tempnam;
use function Safe\file_put_contents;
use function Safe\sprintf;

class ActivityCommandResponse implements ResponseInterface
{
    public function __construct(
        private int $chatId,
        private int $peakValue,
        private string $imageContent
    ) {
    }

    public function process(): void
    {
        Request::sendPhoto($this->toPhoto());
    }

    /**
     * @return array<string,string|int|resource>
     */
    public function toPhoto(): array
    {
        $tmpFilePath = tempnam('/tmp', 'nkitades_whocaresbot');
        file_put_contents($tmpFilePath, $this->imageContent);

        return [
            'chat_id' => $this->chatId,
            'parse_mode' => 'Markdown',
            'caption' => sprintf('*Peak value: %s*', $this->peakValue),
            'photo' => Request::encodeFile($tmpFilePath),
        ];
    }
}
