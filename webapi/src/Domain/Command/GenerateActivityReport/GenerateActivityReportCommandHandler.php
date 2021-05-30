<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\GenerateActivityReport;

use DateInterval;
use Nikitades\WhoCaresBot\WebApi\Domain\RenderedPageProviderInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response\ActivityCommandResponse;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Safe\DateTime;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

use function Safe\ksort;
use function Safe\sprintf;

class GenerateActivityReportCommandHandler implements CommandHandlerInterface
{
    private const DATE_ROUNDED_TO_HOURS_KEY = 'Y-m-d H:00:00';

    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private RenderedPageProviderInterface $renderedPageProvider,
        private CacheInterface $cache,
        private int $cachePeriod
    ) {
    }

    public function __invoke(GenerateActivityReportCommand $command): void
    {
        /** @var ActivityCommandResponse|null $activityCommandResponse */
        $activityCommandResponse = $this->cache->get(
            sprintf('generate_activity_report_command_%s_%s', $command->chatId, $command->withinDays),
            function (ItemInterface $item) use ($command): ActivityCommandResponse {
                $item->expiresAfter($this->cachePeriod);

                $lastMessages = $this->userMessageRecordRepository->getMessagesAggregatedByTime(
                    $command->chatId,
                    ($command->withinDays * 24) - 1,
                    0,
                    UserMessageRecordRepositoryInterface::BY_HOUR
                );

                $firstTimestamp = DateTime::createFromFormat(self::DATE_ROUNDED_TO_HOURS_KEY, (new DateTime('now'))->sub(new DateInterval('P1D'))->format(self::DATE_ROUNDED_TO_HOURS_KEY));
                $lastTimestamp = DateTime::createFromFormat(self::DATE_ROUNDED_TO_HOURS_KEY, (new DateTime('now'))->format(self::DATE_ROUNDED_TO_HOURS_KEY));

                $realChronologicPositionsMap = [];
                for ($i = $firstTimestamp; $i < $lastTimestamp; $i->add(new DateInterval('PT1H'))) {
                    $realChronologicPositionsMap[$i->format(self::DATE_ROUNDED_TO_HOURS_KEY)] = 0;
                }

                foreach ($lastMessages as $messagesAtTimeRecord) {
                    $realChronologicPositionsMap[$messagesAtTimeRecord->time->format(self::DATE_ROUNDED_TO_HOURS_KEY)] = $messagesAtTimeRecord->messagesCount;
                }

                ksort($realChronologicPositionsMap);

                $labels = array_map(
                    fn (string $stringDateRecord): string => DateTime::createFromFormat(self::DATE_ROUNDED_TO_HOURS_KEY, $stringDateRecord)->format('H:00'),
                    array_keys($realChronologicPositionsMap)
                );
                $positions = array_values($realChronologicPositionsMap);

                return new ActivityCommandResponse(
                    chatId: $command->chatId,
                    peakValue: array_reduce($positions, fn (int $carry, int $count) => $carry = $count > $carry ? $count : $carry, 0),
                    imageContent: $this->renderedPageProvider->getActivityImage(
                        labels: $labels,
                        positions: $positions
                    )
                );
            }
        );

        if (null === $activityCommandResponse) {
            return;
        }

        $activityCommandResponse->process();
    }
}
