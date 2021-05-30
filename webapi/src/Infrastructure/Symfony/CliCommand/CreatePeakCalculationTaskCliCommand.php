<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Symfony\CliCommand;

use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\CalculateChatPeak\CalculateChatPeakCommand;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBus;

class CreatePeakCalculationTaskCliCommand extends Command
{
    public function __construct(
        private MessageBus $commandBus,
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository
    ) {
        parent::__construct('bot:cron:calculatePeak');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $chatIds = $this->userMessageRecordRepository->getAliveChatsWithinDays(30);

        foreach ($chatIds as $chatId) {
            $this->commandBus->dispatch(new CalculateChatPeakCommand(
                chatId: $chatId
            ));
        }

        return Command::SUCCESS;
    }
}
