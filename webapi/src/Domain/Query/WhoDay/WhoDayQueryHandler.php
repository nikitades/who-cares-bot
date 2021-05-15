<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Query\WhoDay;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class WhoDayQueryHandler implements MessageHandlerInterface
{
    public function __invoke(WhoDayQuery $query): WhoDayQueryResponse
    {
        return new WhoDayQueryResponse(
            [],
            'a'
        );
    }
}
