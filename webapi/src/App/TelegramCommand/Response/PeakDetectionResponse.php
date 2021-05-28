<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response;

use Longman\TelegramBot\Request;

class PeakDetectionResponse implements ResponseInterface
{
    public function __construct(
        private int $chatId,
        private int $peakValue
    ) {
    }

    public function process(): void
    {
        $text = $this->getText();
        if (null === $text) {
            return;
        }

        Request::sendMessage($this->toMessage($text));
    }

    /**
     * @return array<string,int|string>
     */
    public function toMessage(string $text): array
    {
        return [
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];
    }

    private function getText(): ?string
    {
        switch ($this->peakValue) {
            case 0:
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
                return null;
            case 6:
                return '🌶️ *Wow, that\'s hot!* 🌶️ [peakValue = 6]';
            case 7:
                return '⚔️ *Hoho! One of us is gonna get hurt* ⚔️ [peakValue = 7]';
            case 8:
                return '📈  *IDDQD!!!!* 📈  [peakValue = 8]';
            case 9:
                return '😈 *Hell is empty and all the devils are here!* 😈 [peakValue = 9]';
            case 10:
            default:
                return '🔥 *WORLD WILL NEVER BE THE SAME* 🔥 [peakValue = 10!!!]';
        }
    }
}
