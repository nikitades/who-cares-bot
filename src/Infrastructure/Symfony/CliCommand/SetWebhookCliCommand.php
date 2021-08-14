<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Symfony\CliCommand;

use Nikitades\WhoCaresBot\WebApi\Infrastructure\Telegram\BusAwareTelegram;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use function Safe\sprintf;

class SetWebhookCliCommand extends Command
{
    public function __construct(private BusAwareTelegram $telegram)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bot:webhook:set')
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
            if (is_array($webhook = $input->getArgument('webhook'))) {
                $webhook = array_shift($webhook);
            }
            $webhook = (string) $webhook;
            $this->telegram->setWebhook($webhook);
            (new SymfonyStyle($input, $output))->info(sprintf('Webhook successfully set to: %s', $webhook));
        } catch (Throwable $e) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
