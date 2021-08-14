<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\PeakAnalysis;

use DateInterval;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\ResponseGeneratorInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\TmpFileStorageProviderInterface;
use function Safe\substr;

class PositivePeakAnalysisResponseGenerator implements ResponseGeneratorInterface
{
    public function __construct(
        private TmpFileStorageProviderInterface $tmpFileStorageProvider
    ) {
    }

    public function process(
        int $chatId,
        int $initialMessageId,
        int $messagesCount,
        DateInterval $timeLength,
        float $averageFrequencyPerMinute,
        float $peakFrequencyPerMinute,
        string $mostActivePersonName,
        string $imageContent
    ): void {
        Request::sendPhoto([
            'chat_id' => $chatId,
            'reply_to_message_id' => $initialMessageId,
            'caption' => $this->getText(
                $timeLength,
                $averageFrequencyPerMinute,
                $peakFrequencyPerMinute,
                $mostActivePersonName,
                $messagesCount
            ),
            'photo' => Request::encodeFile($this->tmpFileStorageProvider->storeFileTemporarily($imageContent)),
        ]);
    }

    private function getText(
        DateInterval $timeLength,
        float $averageFrequencyPerMinute,
        float $peakFrequencyPerMinute,
        string $mostActivePersonName,
        int $messagesCount
    ): string {
        return 'It started here ^^^
Peak length: ' . (float) (($timeLength->h * 60 + $timeLength->i) / 60) . ' hours
Average frequency per minute: ' . substr((string) $averageFrequencyPerMinute, 0, 3) . '
Peak frequency per minute: ' . substr((string) $peakFrequencyPerMinute, 0, 3) . '
The most active person: @' . $mostActivePersonName . '
Total messages: ' . $messagesCount;
    }
}
