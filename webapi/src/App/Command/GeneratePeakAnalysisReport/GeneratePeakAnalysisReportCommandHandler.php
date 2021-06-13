<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GeneratePeakAnalysisReport;

use DateTimeInterface;
use LogicException;
use Nikitades\WhoCaresBot\WebApi\App\RenderedPageProviderInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\PeakAnalysis\NegativePeakAnalysisResponseGenerator;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\PeakAnalysis\PositivePeakAnalysisResponseGenerator;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\ChatPeak\ChatPeakRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserMessageRecord;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Safe\DateTime;
use Twig\Environment;
use function Safe\arsort;
use function Safe\sort;

class GeneratePeakAnalysisReportCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private ChatPeakRepositoryInterface $chatPeakRepository,
        private Environment $twigEnvironment,
        private RenderedPageProviderInterface $renderedPageProvider,
        private NegativePeakAnalysisResponseGenerator $negativePeakAnalysisResponseGenerator,
        private PositivePeakAnalysisResponseGenerator $positivePeakAnalysisResponseGenerator
    ) {
    }

    public function __invoke(GeneratePeakAnalysisReportCommand $command): void
    {
        $messagesAggregated = $this->userMessageRecordRepository->getMessagesAggregatedByTime(
            chatId: $command->chatId,
            withinHours: $command->withinDays * 24,
            offsetHours: 0,
            interval: UserMessageRecordRepositoryInterface::BY_HOUR
        );

        if ([] === $messagesAggregated) {
            $this->negativePeakAnalysisResponseGenerator->process(
                $command->chatId,
                NegativePeakAnalysisResponseGenerator::REASON_NOT_ENOUGH_DATA_COLLECTED
            );

            return;
        }

        $peak = $this->findPeakWithin(period: $messagesAggregated);

        $historicalPeak = $this->chatPeakRepository->findLastByChatId($command->chatId);

        if (null === $historicalPeak) {
            $this->negativePeakAnalysisResponseGenerator->process(
                $command->chatId,
                NegativePeakAnalysisResponseGenerator::REASON_NOT_ENOUGH_DATA_COLLECTED
            );

            return;
        }

        if ($peak->messagesCount < $historicalPeak->getPeak() / 2) {
            $this->negativePeakAnalysisResponseGenerator->process(
                $command->chatId,
                NegativePeakAnalysisResponseGenerator::REASON_NO_PEAKS_DETECTED
            );

            return;
        }

        $peakStart = $this->findPeakStart(
            peak: $peak,
            period: $messagesAggregated
        );

        $peakEnd = $this->findPeakEnd(
            peak: $peak,
            period: $messagesAggregated
        );

        if (null === $peakStart) {
            $peakStart = $this->findPeakStart(
                peak: $peak,
                period: $this->userMessageRecordRepository->getMessagesAggregatedByTime(
                    chatId: $command->chatId,
                    withinHours: $command->withinDays * 24 * 2,
                    offsetHours: $command->withinDays * 24,
                    interval: UserMessageRecordRepositoryInterface::BY_HOUR
                )
            );
        }

        if (null === $peakStart) {
            $this->negativePeakAnalysisResponseGenerator->process(
                $command->chatId,
                NegativePeakAnalysisResponseGenerator::REASON_NO_PEAKS_DETECTED
            );

            return;
        }

        $hoursIntervalToPeakStart = (new DateTime('now'))->diff($peakStart)->h + 1;
        $hoursIntervalToPeakEnd = (new DateTime('now'))->diff($peakEnd)->h - 1;

        $recordsIncludingStartMessageRough = $this->userMessageRecordRepository->getAllRecordsWithinHours(
            chatId: $command->chatId,
            withinHours: $hoursIntervalToPeakStart,
            offsetHours: $hoursIntervalToPeakEnd
        );

        if (0 === count($recordsIncludingStartMessageRough)) {
            $this->negativePeakAnalysisResponseGenerator->process(
                $command->chatId,
                NegativePeakAnalysisResponseGenerator::REASON_NOT_ENOUGH_DATA_COLLECTED
            );

            return;
        }

        $targetMessage = $this->findFirstMessageNearTimestamp(
            timestamp: $peakStart,
            messages: $recordsIncludingStartMessageRough
        );

        $messagesWithinPeakOnly = $this->getMessagesGroupWithinDates(
            messages: $recordsIncludingStartMessageRough,
            firstDate: $peakStart,
            lastDate: $peakEnd
        );

        $labels = [];
        $positions = [];

        $this->positivePeakAnalysisResponseGenerator->process(
            chatId: $command->chatId,
            initialMessageId: $targetMessage?->getMessageId() ?? 0,
            messagesCount: count($messagesWithinPeakOnly),
            timeLength: $peakStart->diff($peakEnd),
            averageFrequencyPerMinute: (float) (count($messagesWithinPeakOnly) / ($peakStart->diff($peakEnd)->h * 60 + $peakStart->diff($peakEnd)->m)),
            peakFrequencyPerMinute: $peak->messagesCount / 60,
            mostActivePersonName: $this->getMostActivePersonName($messagesWithinPeakOnly),
            imageContent: $this->renderedPageProvider->getPeakLogImage(
                labels: $labels,
                positions: $positions
            )
        );
    }

    /**
     * @param array<MessagesAtTimeCount> $period
     */
    private function findPeakWithin(array $period): MessagesAtTimeCount
    {
        /** @var MessagesAtTimeCount $biggestGroup */
        $biggestGroup = array_reduce(
            $period,
            fn (?MessagesAtTimeCount $carry, MessagesAtTimeCount $group) => $group->messagesCount > $carry?->messagesCount ?? 0 ? $group : $carry,
            null
        );

        return $biggestGroup;
    }

    /**
     * @param array<MessagesAtTimeCount> $period
     */
    private function findPeakStart(MessagesAtTimeCount $peak, array $period): ?DateTimeInterface
    {
        /** @var array<int> $periodSum */
        $periodSum = array_map(
            fn (MessagesAtTimeCount $count): int => $count->messagesCount,
            $period
        );

        sort($periodSum);

        $periodMedian = $periodSum[count($periodSum) / 2 - 1] * 0.5;

        $peakHasStarted = false;
        for ($i = count($period) - 1; $i > 0; --$i) {
            $currentMessagesGroup = $period[$i];
            if (!$peakHasStarted && $currentMessagesGroup->time <= $peak->time) {
                $peakHasStarted = true;
            }

            if ($peakHasStarted && $currentMessagesGroup->messagesCount <= $periodMedian) {
                return $currentMessagesGroup->time;
            }
        }

        return null;
    }

    /**
     * @param array<MessagesAtTimeCount> $period
     */
    private function findPeakEnd(MessagesAtTimeCount $peak, array $period): DateTimeInterface
    {
        /** @var array<int> $periodSum */
        $periodSum = array_map(
            fn (MessagesAtTimeCount $count): int => $count->messagesCount,
            $period
        );

        sort($periodSum);

        $periodMedian = $periodSum[count($periodSum) / 2 - 1] * 0.5;

        $peakHasStarted = false;
        for ($i = 0; $i < count($period); ++$i) {
            $currentMessagesGroup = $period[$i];
            if (!$peakHasStarted && $currentMessagesGroup->time > $peak->time) {
                $peakHasStarted = true;
            }

            if ($peakHasStarted && $currentMessagesGroup->messagesCount <= $periodMedian) {
                return $currentMessagesGroup->time;
            }
        }

        return $period[count($period) - 1]->time;
    }

    /**
     * @param array<UserMessageRecord> $messages
     */
    private function findFirstMessageNearTimestamp(DateTimeInterface $timestamp, array $messages): ?UserMessageRecord
    {
        foreach (array_reverse($messages) as $message) {
            if ($message->getCreatedAt() <= $timestamp) {
                return $message;
            }
        }

        return null;
    }

    /**
     * @param array<UserMessageRecord> $messages
     * @return array<UserMessageRecord>
     */
    private function getMessagesGroupWithinDates(array $messages, DateTimeInterface $firstDate, DateTimeInterface $lastDate): array
    {
        $output = [];

        foreach ($messages as $message) {
            if ($message->getCreatedAt() > $firstDate && $message->getCreatedAt() < $lastDate) {
                $output[] = $message;
            }
        }

        return $output;
    }

    /**
     * @param array<UserMessageRecord> $messages
     */
    private function getMostActivePersonName(array $messages): string
    {
        if (0 === count($messages)) {
            throw new LogicException('Empty array given');
        }

        /** @var array<string,int> */
        $counter = [];

        foreach ($messages as $message) {
            if (!isset($counter[$message->getUserNickname()])) {
                $counter[$message->getUserNickname()] = 0;
            }

            ++$counter[$message->getUserNickname()];
        }

        arsort($counter);

        /** @var array<string> */
        $keys = array_keys($counter);

        return $keys[0];
    }
}
