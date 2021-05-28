<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\GeneratePeakAnalysisReport;

use Longman\TelegramBot\Request;

use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Twig\Environment;

class GeneratePeakAnalysisReportCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private Environment $twigEnvironment
    ) {
    }

    public function __invoke(GeneratePeakAnalysisReportCommand $command): void
    {
        $messageRecords = $this->userMessageRecordRepository->getAllRecordsWithinDays($command->chatId, $command->withinDays);

        Request::sendMessage([
            'chat_id' => $command->chatId,
            'text' => 'privet',
        ]);
    }
}
