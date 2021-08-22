<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GenerateActivityReport;

use mikehaertl\wkhtmlto\Image;
use function Safe\sprintf;
use function Safe\ksort;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Safe\DateTime;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\Activity\ActivityResponseGenerator;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\Activity\ActivityResponse;
use DateInterval;
use RuntimeException;
use Twig\Environment;

class GenerateActivityReportCommandHandler implements CommandHandlerInterface
{
    private const DATE_ROUNDED_TO_HOURS_KEY = 'Y-m-d H:00:00';

    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private CacheInterface $cache,
        private ActivityResponseGenerator $activityResponseGenerator,
        private Environment $twigEnvironment,
        private int $cachePeriod
    ) {
    }

    public function __invoke(GenerateActivityReportCommand $command): void
    {
        /** @var ActivityResponse|null $activityResponse */
        $activityResponse = $this->cache->get(
            sprintf('generate_activity_report_command_%s_%s', $command->chatId, $command->withinDays),
            function (ItemInterface $item) use ($command): ActivityResponse {
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

                $image = new Image(
                    $this->twigEnvironment->render('activity.twig', ['labels' => $labels, 'data' => $positions])
                );
                $image->setOptions([
                    'width' => 800,
                    'height' => 680,
                    'zoom' => 1,
                    'format' => 'png',
                    'javascript-delay' => 50,
                    'no-stop-slow-scripts',
                ]);

                $imageContent = $image->toString();

                if (is_bool($imageContent)) {
                    throw new RuntimeException('Failed to create the image!');
                }

                return new ActivityResponse(
                    chatId: $command->chatId,
                    peakValue: array_reduce($positions, fn (int $carry, int $count) => $carry = $count > $carry ? $count : $carry, 0),
                    imageContent: $imageContent
                );
            }
        );

        if (null === $activityResponse) {
            return;
        }

        $this->activityResponseGenerator->process($activityResponse);
    }
}
