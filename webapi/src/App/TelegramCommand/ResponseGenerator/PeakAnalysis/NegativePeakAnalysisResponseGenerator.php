<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\PeakAnalysis;

use LogicException;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\ResponseGeneratorInterface;

class NegativePeakAnalysisResponseGenerator implements ResponseGeneratorInterface
{
    public const REASON_NOT_ENOUGH_DATA_COLLECTED = 'REASON_NOT_ENOUGH_MESSAGES';
    public const REASON_NO_PEAKS_DETECTED = 'REASON_NO_PEAKS_DETECTED';

    public function process(
        int $chatId,
        string $reasonCode
    ): void {
        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $this->getMessage($reasonCode),
        ]);
    }

    private function getMessage(string $reasonCode): string
    {
        return match ($reasonCode) {
            self::REASON_NOT_ENOUGH_DATA_COLLECTED => 'Sorry, there\'s not enough data collected in this chat yet.',
            self::REASON_NO_PEAKS_DETECTED => 'Sorry, no significant peaks were registered recently.',
            default => throw new LogicException('Unknown reason!')
        };
    }
}
