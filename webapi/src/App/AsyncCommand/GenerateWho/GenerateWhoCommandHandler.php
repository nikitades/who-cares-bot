<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\GenerateWho;

use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\RenderedPageProviderInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response\WhoCommandResponse;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserPosition;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use function Safe\sprintf;

class GenerateWhoCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private RenderedPageProviderInterface $renderedPageProvider,
        private CacheInterface $cache
    ) {
    }

    public function __invoke(GenerateWhoCommand $command): void
    {
        /** @var WhoCommandResponse $whoCommandResponse */
        $whoCommandResponse = $this->cache->get(
            sprintf('generate_who_command_%s_%s_%s', $command->chatId, $command->daysAmount, 6),
            function (ItemInterface $item) use ($command): WhoCommandResponse {
                $item->expiresAfter(300);
                $positions = $this->userMessageRecordRepository->findPositionsWithinDays(
                    chatId: $command->chatId,
                    withinHours: $command->daysAmount * 24,
                    topUsersCount: 6
                );

                return new WhoCommandResponse(
                    $positions,
                    $command->chatId,
                    $this->renderedPageProvider->getRegularTopImage(
                        array_map(
                            fn (UserPosition $position): string => sprintf('%s: %s', $position->userNickname, $position->userMessagesCount),
                            $positions
                        ),
                        array_map(
                            fn (UserPosition $position): int => $position->userMessagesCount,
                            $positions
                        )
                    )
                );
            }
        );

        $whoCommandResponse->process();
    }
}
