<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Symfony\CliCommand;

use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\CalculateChatMedian\CalculateChatMedianCommand;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBus;

class CreateMedianCalculationTaskCliCommand extends Command
{
    public function __construct(
        private MessageBus $commandBus,
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository
    ) {
        parent::__construct('bot:cron:calculateMedian');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $chatIds = $this->userMessageRecordRepository->getAliveChatsWithinDays(30);

        foreach ($chatIds as $chatId) {
            $this->commandBus->dispatch(new CalculateChatMedianCommand(
                chatId: $chatId
            ));
        }

        return Command::SUCCESS;
    }
}
