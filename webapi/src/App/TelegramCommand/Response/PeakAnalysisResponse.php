<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response;

use DateInterval;
use Longman\TelegramBot\Request;
use function Safe\substr;

class PeakAnalysisResponse implements ResponseInterface
{
    public function __construct(
        private int $chatId,
        private int $initialMessageId,
        private int $messagesCount,
        private DateInterval $timeLength,
        private float $averageFrequencyPerMinute,
        private float $peakFrequencyPerMinute,
        private string $mostActivePersonName
    ) {
    }

    public function process(): void
    {
        Request::sendMessage($this->toMessage());
    }

    /**
     * @return array<string,string|int>
     */
    public function toMessage(): array
    {
        return [
            'chat_id' => $this->chatId,
            'reply_to_message_id' => $this->initialMessageId,
            'text' => $this->getText(),
        ];
    }

    private function getText(): string
    {
        return 'All started here ^^^
Peak length: ' . (float) (($this->timeLength->h * 60 + $this->timeLength->i) / 60) . ' hours
Average frequency per minute: ' . substr((string) $this->averageFrequencyPerMinute, 0, 3) . '
Peak frequency per minute: ' . substr((string) $this->peakFrequencyPerMinute, 0, 3) . '
The most active person: @' . $this->mostActivePersonName;
    }
}
