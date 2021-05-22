<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Test\Unit\Infrastructure\Telegram\Renderer;

use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who\WhoCommandResponseRenderRequest;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
use Nikitades\WhoCaresBot\WebApi\Infrastructure\Telegram\Renderer\WhoCommandResponseRenderer;
use PHPUnit\Framework\TestCase;

class WhoCommandResponseRendererTest extends TestCase
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

        $result = (new WhoCommandResponseRenderer())(new WhoCommandResponseRenderRequest(
            $desiredChatId,
            [$userPosition1, $userPosition2]
        ));

        static::assertEquals([
            'chat_id' => $desiredChatId,
            'parse_mode' => 'Markdown',
            'text' => '**someUser1**: 5
**someUser2**: 7',
        ], $result);
    }
}
