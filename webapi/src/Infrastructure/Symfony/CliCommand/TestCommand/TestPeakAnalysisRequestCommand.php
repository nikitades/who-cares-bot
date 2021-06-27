<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Symfony\CliCommand\TestCommand;

use Nikitades\WhoCaresBot\WebApi\App\Command\GeneratePeakAnalysisReport\GeneratePeakAnalysisReportCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class TestPeakAnalysisRequestCommand extends Command
{
    public function __construct(private MessageBusInterface $commandBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bot:test:peakAnalysis')
            ->setDescription('Test peak analysis command')
            ->addArgument(
                'chatId',
                InputArgument::REQUIRED,
                'Chat Id to simulate the request from'
            )
            ->addArgument(
                'daysPeriod',
                InputArgument::REQUIRED,
                'Days period to seek within'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->commandBus->dispatch(new GeneratePeakAnalysisReportCommand(
            chatId: (int) $input->getArgument('chatId'),
            withinDays: (int) $input->getArgument('daysPeriod')
        ));

        return Command::SUCCESS;
    }
}