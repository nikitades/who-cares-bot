<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response;

use Longman\TelegramBot\Request;

use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserPosition;
use function Safe\file_put_contents;
use function Safe\sprintf;
use function Safe\tempnam;

class WhoCommandResponse implements ResponseInterface
{
    private string $text;

    /**
     * @param array<UserPosition> $userPositions
     */
    public function __construct(
        private array $userPositions,
        private int $chatId,
        private string $imageContent
    ) {
        $this->text = 0 === count($userPositions) ? 'No messages registered!' : implode(
            "\n",
            array_map(
                fn (UserPosition $userPosition): string => sprintf('*@%s*: %s', $userPosition->userNickname, $userPosition->userMessagesCount),
                $userPositions
            ),
        );
    }

    public function process(): void
    {
        Request::sendPhoto($this->toSendPhoto());
    }

    /**
     * @return array<string,int|string|resource>
     */
    public function toSendPhoto(): array
    {
        $tmpFilePath = tempnam('/tmp', 'nkitades_whocaresbot');
        file_put_contents($tmpFilePath, $this->imageContent);

        return [
            'chat_id' => $this->chatId,
            'parse_mode' => 'Markdown',
            'caption' => $this->text,
            'photo' => Request::encodeFile($tmpFilePath),
        ];
    }
}
