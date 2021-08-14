<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\Who;

use Longman\TelegramBot\Request;

use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\ResponseGeneratorInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\TmpFileStorageProviderInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserPosition;
use function Safe\sprintf;

class WhoCommandResponseGenerator implements ResponseGeneratorInterface
{
    public function __construct(
        private TmpFileStorageProviderInterface $tmpFileStorageProvider
    ) {
    }

    public function process(WhoCommandResponse $whoCommandResponse): void
    {
        Request::sendPhoto([
            'chat_id' => $whoCommandResponse->chatId,
            'parse_mode' => 'Markdown',
            'caption' => 0 === count($whoCommandResponse->userPositions) ? 'No messages registered!' : implode(
                "\n",
                array_map(
                    fn (UserPosition $userPosition): string => sprintf('*@%s*: %s', $userPosition->userNickname, $userPosition->userMessagesCount),
                    $whoCommandResponse->userPositions
                ),
            ),
            'photo' => Request::encodeFile($this->tmpFileStorageProvider->storeFileTemporarily($whoCommandResponse->imageContent)),
        ]);
    }
}
