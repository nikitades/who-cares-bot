<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Test\Unit\App\TelegramCommand\User\Who;

use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who\WhoCommandResponse;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
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
            $desiredChatId
        ))->toArray();

        static::assertEquals([
            'chat_id' => $desiredChatId,
            'parse_mode' => 'Markdown',
            'text' => '**someUser1**: 5
**someUser2**: 7',
        ], $result);
    }
}
