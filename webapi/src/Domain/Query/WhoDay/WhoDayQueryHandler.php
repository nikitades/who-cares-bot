<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay;

use Nikitades\WhoCaresBot\WebApi\Domain\Query\QueryHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;

class WhoDayQueryHandler implements QueryHandlerInterface
{
    public function __construct(private UserMessageRecordRepositoryInterface $userRecordRepository)
    {
    }

    public function __invoke(WhoDayQuery $query): WhoDayQueryResponse
    {
        /** @var array<UserPosition> $userPositions */
        $userPositions = $this->userRecordRepository->findPositionsWithinDays($query->chatId, 1, 4);

        return new WhoDayQueryResponse(
            userPositions: $userPositions,
            imageContent: 'a'
        );
    }
}
