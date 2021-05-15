<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Symfony\CliCommand;

use Longman\TelegramBot\Telegram;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class SetWebhookCliCommand extends Command
{
    public function __construct(private Telegram $telegram)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('webhook:set')
            ->setDescription('Set webhook')
            ->addArgument(
                'webhook',
                InputArgument::REQUIRED,
                'Get it from @BotFather'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $webhook = (string) $input->getArgument('webhook');
            $this->telegram->setWebhook($webhook);
            (new SymfonyStyle($input, $output))->info(sprintf('Webhook successfully set to: %s', $webhook));
        } catch (Throwable $e) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
