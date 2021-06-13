<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator;

use Longman\TelegramBot\Request;

class PeakDetectionResponseGenerator implements ResponseGeneratorInterface
{
    public function process(
        int $chatId,
        int $peakValue
    ): void {
        $text = $this->getText($peakValue);
        if (null === $text) {
            return;
        }

        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }

    private function getText(int $peakValue): ?string
    {
        switch ($peakValue) {
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
