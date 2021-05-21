<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Test\Unit\Domain\Query\Who;

use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\Who\WhoDayQuery;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\Who\WhoQueryHandler;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\Who\WhoWeekQuery;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use PHPUnit\Framework\TestCase;

class WhoQueryHandlerTest extends TestCase
{
    /**
     * @dataProvider getTestData
     */
    public function testHandleQuery(
        int $chatId,
        int $daysCount,
        int $topUsersCount,
        string $userNickName,
        int $userId,
        int $desiredMessagesCount,
        string $queryClass
    ): void {
        $repository = $this->createMock(UserMessageRecordRepositoryInterface::class);
        $repository->expects(static::once())
            ->method('findPositionsWithinDays')
            ->with(
                $chatId,
                $daysCount,
                $topUsersCount
            )
            ->willReturn([
                new UserPosition(
                    $userNickName,
                    $userId,
                    $desiredMessagesCount
                ),
            ]);

        $whoQueryHandler = new WhoQueryHandler($repository);

        $result = $whoQueryHandler(new $queryClass(
            $chatId,
            $userId
        ));

        static::assertNotEmpty($result->userPositions);
        static::assertContainsOnly(UserPosition::class, $result->userPositions);
        static::assertEquals($userNickName, $result->userPositions[0]->userNickname);
        static::assertEquals($userId, $result->userPositions[0]->userId);
        static::assertEquals($desiredMessagesCount, $result->userPositions[0]->userMessagesCount);
    }

    /**
     * @return array<array<string,int|string>>
     */
    public function getTestData(): array
    {
        return [
            [
                'chatId' => 1,
                'daysCount' => 1,
                'topUsersCount' => 4,
                'userNickName' => 'someUserNickName',
                'userId' => 1,
                'desiredMessagesCount' => 5,
                'queryClass' => WhoDayQuery::class,
            ],
            [
                'chatId' => 1,
                'daysCount' => 7,
                'topUsersCount' => 4,
                'userNickName' => 'someUserNickName',
                'userId' => 1,
                'desiredMessagesCount' => 5,
                'queryClass' => WhoWeekQuery::class,
            ],
        ];
    }
}
