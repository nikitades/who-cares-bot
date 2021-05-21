<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Query\Who;

use Nikitades\WhoCaresBot\WebApi\Domain\Query\QueryHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;

class WhoQueryHandler implements QueryHandlerInterface
{
    public function __construct(private UserMessageRecordRepositoryInterface $userRecordRepository)
    {
    }

    public function __invoke(WhoDayQuery | WhoWeekQuery $query): WhoQueryResponse
    {
        /** @var array<UserPosition> $userPositions */
        $userPositions = $this->userRecordRepository->findPositionsWithinDays(
            $query->chatId,
            match ($query::class) {
                WhoDayQuery::class => 1,
                WhoWeekQuery::class => 7,
                default => 1
            },
            4
        );

        return new WhoQueryResponse(
            userPositions: $userPositions,
            imageContent: 'a'
        );
    }
}
