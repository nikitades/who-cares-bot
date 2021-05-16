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
        return new WhoDayQueryResponse(
            [
                new UserPosition(
                    'someUser',
                    213412312,
                    33
                ),
            ],
            'a'
        );
    }
}
