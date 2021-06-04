<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\Domain\Command\GeneratePeakAnalysisReport;

use DateTimeInterface;
use Exception;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\ChatPeak\ChatPeakRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\GeneratePeakAnalysisReport\GeneratePeakAnalysisReportCommand;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\MessagesAtTimeCount;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserMessageRecord;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Safe\DateTime;
use Twig\Environment;
use function Safe\krsort;

class GeneratePeakAnalysisReportCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private ChatPeakRepositoryInterface $chatPeakRepository,
        private Environment $twigEnvironment
    ) {
    }

    public function __invoke(GeneratePeakAnalysisReportCommand $command): void
    {
        $messagesAggregated = $this->userMessageRecordRepository->getMessagesAggregatedByTime(
            chatId: $command->chatId,
            withinHours: $command->withinDays * 24,
            offsetHours: 0,
            interval: UserMessageRecordRepositoryInterface::BY_MINUTE
        );

        if ([] === $messagesAggregated) {
            return;
        }

        /**
         * 1. Найти пик среди сообщений
         * 2. Если пик ниже чем половина прошлого, то ответ отрицательный
         * 3. Идти влево от пика, чтобы найти, где линия касается медианы по выборке
         * 4. Если найдено, то публикуем инфу:
         *      - спайк длиной n
         *      - длительностью n
         *      - средняя частота n
         *      - самый активный n
         * 5. Если так и не коснулось, то запрос предыдущего куска, и поиск по нему со старыми данными.
         */
        $peak = $this->findPeakWithin(period: $messagesAggregated);

        $historicalPeak = $this->chatPeakRepository->findLastByChatId($command->chatId);

        if (null === $historicalPeak) {
            //TODO: ответить что недостаточно истории чата
            return;
        }

        if ($peak->messagesCount < $historicalPeak->getPeak() / 2) {
            //TODO: ответить что не удается выявить явные пики в текущий момент
            return;
            //TODO: получить статистический пик по чату, сравнить текущий пик со статистическим, больше ли он половины от статистического пика
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
                    interval: UserMessageRecordRepositoryInterface::BY_MINUTE
                )
            );
        }

        if (null === $peakStart || null === $peakEnd) {
            //TODO: Ответ что при поиске начала пика что-то пошло не так
            return;
        }

        $hoursIntervalToPeakStart = (new DateTime('now'))->diff($peakStart)->h + 1;
        $hoursIntervalToPeakEnd = (new DateTime('now'))->diff($peakEnd)->h - 1;

        $recordsIncludingStartMessageRough = $this->userMessageRecordRepository->getAllRecordsWithinHours(
            chatId: $command->chatId,
            withinHours: $hoursIntervalToPeakStart,
            offsetHours: $hoursIntervalToPeakEnd
        );

        $targetMessage = $this->findFirstMessageNearTimestamp(
            timestamp: $peakStart,
            messages: $recordsIncludingStartMessageRough
        );

        if (null === $targetMessage) {
            throw new Exception('Message was not found!');
        }

        $messagesWithinPeakOnly = $this->getMessagesGroupWithinDates(
            messages: $recordsIncludingStartMessageRough,
            firstDate: $peakStart,
            lastDate: $peakEnd
        );

        $peakAnalysisReport = new PeakAnalysisReport(
            messagesCount: count($messagesWithinPeakOnly),
            timeLength: $peakStart->diff($peakEnd),
            averageFrequencyPerMinute: count($messagesWithinPeakOnly) / $peakStart->diff($peakEnd)->m,
            mostActivePersonName: $this->getMostActivePersonName($messagesWithinPeakOnly)
        );

        $text = 'All started here ^^^
Peak length: ' . (($peakAnalysisReport->timeLength->h * 60 + $peakAnalysisReport->timeLength->i) / 60) . ' minutes
Average frequency per minute: ' . $peakAnalysisReport->averageFrequencyPerMinute . '
The most active person: @' . $peakAnalysisReport->mostActivePersonName;

        Request::sendMessage([
            'chat_id' => $command->chatId,
            'text' => $text,
            'reply_to_message_id' => $targetMessage->getMessageId(),
        ]);
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

        $periodMedian = $periodSum[count($periodSum) / 2 - 1];

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
    private function findPeakEnd(MessagesAtTimeCount $peak, array $period): ?DateTimeInterface
    {
        /** @var array<int> $periodSum */
        $periodSum = array_map(
            fn (MessagesAtTimeCount $count): int => $count->messagesCount,
            $period
        );

        $periodMedian = $periodSum[count($periodSum) / 2 - 1];

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

        return null;
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
        /** @var array<string,int> */
        $counter = [];

        foreach ($messages as $message) {
            if (!isset($counter[$message->getUserNickname()])) {
                $counter[$message->getUserNickname()] = 0;
            }

            ++$counter[$message->getUserNickname()];
        }

        krsort($counter);

        /** @var array<string> $keys */
        $keys = array_keys($counter);

        return $keys[0];
    }
}
