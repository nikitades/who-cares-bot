<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Test\Unit\App\TelegramCommand\Response;

use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response\WhoCommandResponse;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserPosition;
use PHPUnit\Framework\TestCase;

class WhoCommandResponseTest extends TestCase
{
    public function testRender(): void
    {
        $desiredChatId = 123;
        $userPosition1 = new UserPosition(
            'someUser1',
            222,
            5
        );
        $userPosition2 = new UserPosition(
            'someUser2',
            333,
            7
        );

        $result = (new WhoCommandResponse(
            [$userPosition1, $userPosition2],
            $desiredChatId,
            ''
        ))->toSendPhoto();

        static::assertArrayHasKey('photo', $result);
        static::assertEquals([
            'chat_id' => $desiredChatId,
            'parse_mode' => 'Markdown',
            'caption' => '*@someUser1*: 5
*@someUser2*: 7',
            'photo' => $result['photo'],
        ], $result);
    }
}
