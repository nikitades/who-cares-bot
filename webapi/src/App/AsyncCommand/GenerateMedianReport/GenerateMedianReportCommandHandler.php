<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\GenerateMedianReport;

use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\RenderedPageProviderInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response\MedianCommandResponse;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use function Safe\sprintf;

class GenerateMedianReportCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private RenderedPageProviderInterface $renderedPageProvider,
        private CacheInterface $cache,
        private int $cachePeriod
    ) {
    }

    public function __invoke(GenerateMedianReportCommand $command): void
    {
        /** @var MedianCommandResponse $medianCommandResponse */
        $medianCommandResponse = $this->cache->get(
            sprintf('generate_median_command_%s_%s', $command->chatId, $command->withinDays),
            function (ItemInterface $item) use ($command): MedianCommandResponse {
                $item->expiresAfter($this->cachePeriod);

                $lastMessages = $this->userMessageRecordRepository->getMessagesAggregatedByTime(
                    $command->chatId,
                    $command->withinDays * 24,
                    UserMessageRecordRepositoryInterface::BY_HOUR
                );

                return new MedianCommandResponse(
                    chatId: $command->chatId,
                    peakValue: array_reduce(
                        $lastMessages,
                        fn (int $carry, MessagesAtTimeCount $count) => $carry = $count->messagesCount > $carry ? $count->messagesCount : $carry,
                        0
                    ),
                    imageContent: $this->renderedPageProvider->getMedianImage(
                        labels: array_map(
                            fn (MessagesAtTimeCount $count): string => $count->time->format('H:i:s'),
                            $lastMessages
                        ),
                        positions: array_map(
                            fn (MessagesAtTimeCount $count): int => $count->messagesCount,
                            $lastMessages
                        )
                    )
                );
            }
        );

        $medianCommandResponse->process();
    }
}
