<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay;

use Nikitades\WhoCaresBot\WebApi\Domain\Query\UserPosition;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class WhoDayQueryHandler implements MessageHandlerInterface
{
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
