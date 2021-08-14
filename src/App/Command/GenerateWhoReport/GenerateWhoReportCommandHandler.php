<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GenerateWhoReport;

use Nikitades\WhoCaresBot\WebApi\App\RenderedPageProviderInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\Who\WhoCommandResponse;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\Who\WhoCommandResponseGenerator;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserPosition;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use function Safe\sprintf;

class GenerateWhoReportCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private RenderedPageProviderInterface $renderedPageProvider,
        private CacheInterface $cache,
        private WhoCommandResponseGenerator $whoCommandResponseGenerator,
        private int $cachePeriod
    ) {
    }

    public function __invoke(GenerateWhoReportCommand $command): void
    {
        $topUsersCount = 6;
        /** @var WhoCommandResponse $whoCommandResponse */
        $whoCommandResponse = $this->cache->get(
            sprintf('generate_who_command_%s_%s_%s', $command->chatId, $command->withinDays, $topUsersCount),
            function (ItemInterface $item) use ($command, $topUsersCount): WhoCommandResponse {
                $item->expiresAfter($this->cachePeriod);

                $positions = $this->userMessageRecordRepository->findPositionsWithinHours(
                    chatId: $command->chatId,
                    withinHours: $command->withinDays * 24,
                    topUsersCount: $topUsersCount
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

        $this->whoCommandResponseGenerator->process($whoCommandResponse);
    }
}
