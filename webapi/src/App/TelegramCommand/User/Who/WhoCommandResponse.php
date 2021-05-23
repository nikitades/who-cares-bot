<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who;

use Longman\TelegramBot\Request;

use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
use function Safe\sprintf;
use function Safe\tempnam;
use function Safe\file_put_contents;

class WhoCommandResponse
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
                fn (UserPosition $userPosition): string => sprintf('**%s**: %s', $userPosition->userNickname, $userPosition->userMessagesCount),
                $userPositions
            ),
        );
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
