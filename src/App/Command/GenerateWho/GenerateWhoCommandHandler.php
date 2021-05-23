<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GenerateWho;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who\WhoCommandResponse;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use function Safe\sprintf;

class GenerateWhoCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private CacheInterface $cache
    ) {
    }

    public function __invoke(GenerateWhoCommand $command): ServerResponse
    {
        $whoCommandResponse = $this->cache->get(
            sprintf('generate_who_command_%s_%s_%s', $command->chatId, $command->daysAmount, 4),
            function (ItemInterface $item) use ($command): WhoCommandResponse {
                $item->expiresAfter(30);
                return new WhoCommandResponse(
                    $this->userMessageRecordRepository->findPositionsWithinDays($command->chatId, $command->daysAmount, 4),
                    $command->chatId
                );
            }
        );

        return Request::sendMessage($whoCommandResponse->toArray());
    }
}
