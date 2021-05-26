<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Domain\Command\StartThrottledPeakDetection;

use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\DetectPeak\DetectPeakCommand;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class StartThrottledPeakDetectionCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private RateLimiterFactory $anonymousApiLimiter,
        private MessageBusInterface $commandBus
    ) {
    }

    public function __invoke(StartThrottledPeakDetectionCommand $command): void
    {
        $limiter = $this->anonymousApiLimiter->create((string) $command->chatId);

        if (false === $limiter->consume(1)->isAccepted()) {
            return;
        }

        $this->commandBus->dispatch(new DetectPeakCommand($command->chatId));
    }
}
