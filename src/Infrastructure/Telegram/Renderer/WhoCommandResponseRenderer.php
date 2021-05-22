<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Telegram\Renderer;

use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseRendererInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who\WhoCommandResponseRenderRequest;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
use function Safe\sprintf;

class WhoCommandResponseRenderer implements ResponseRendererInterface
{
    /**
     * @return array<string,int|string>
     */
    public function __invoke(WhoCommandResponseRenderRequest $renderRequest): array
    {
        $text = 0 === count($renderRequest->userPositions) ? 'No messages registered!' : implode(
            "\n",
            array_map(
                fn (UserPosition $userPosition): string => sprintf('**%s**: %s', $userPosition->userNickname, $userPosition->userMessagesCount),
                $renderRequest->userPositions
            ),
        );

        return [
            'chat_id' => $renderRequest->chatId,
            'parse_mode' => 'Markdown',
            'text' => $text,
            //TODO[image chart attach]
        ];
    }
}
