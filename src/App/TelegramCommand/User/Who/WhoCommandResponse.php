<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who;

use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
use function Safe\sprintf;

class WhoCommandResponse
{
    private string $text;

    /**
     * @param array<UserPosition> $userPositions
     */
    public function __construct(
        private array $userPositions,
        private int $chatId
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
     * @return array<string,int|string>
     */
    public function toArray(): array
    {
        return [
            'chat_id' => $this->chatId,
            'parse_mode' => 'Markdown',
            'text' => $this->text,
            //TODO[image chart attach]
        ];
    }
}
