<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Command\GenerateWhoReport;

use mikehaertl\wkhtmlto\Image;
use function Safe\sprintf;
use Twig\Environment;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserPosition;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\UserMessageRecord\UserMessageRecordRepositoryInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Command\CommandHandlerInterface;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\Who\WhoCommandResponseGenerator;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\ResponseGenerator\Who\WhoCommandResponse;
use RuntimeException;

class GenerateWhoReportCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserMessageRecordRepositoryInterface $userMessageRecordRepository,
        private CacheInterface $cache,
        private WhoCommandResponseGenerator $whoCommandResponseGenerator,
        private Environment $twigEnvironment,
        private int $cachePeriod
    ) {
    }

    public function __invoke(GenerateWhoReportCommand $command): void
    {
        $topUsersCount = 6;
        /** @var WhoCommandResponse $whoCommandResponse */
        $whoCommandResponse = $this->cache->get(
            sprintf('generate_who_command_%s_%s_%s', $command->chatId, $command->withinDays, $topUsersCount),
            function (ItemInterface $item) use ($command, $topUsersCount): WhoCommandResponse {
                $item->expiresAfter($this->cachePeriod);

                $positions = $this->userMessageRecordRepository->findPositionsWithinHours(
                    chatId: $command->chatId,
                    withinHours: $command->withinDays * 24,
                    topUsersCount: $topUsersCount
                );

                $image = new Image(
                    $this->twigEnvironment->render(
                        'top.twig',
                        [
                            'labels' => array_map(
                                fn (UserPosition $position): string => sprintf('%s: %s', $position->userNickname, $position->userMessagesCount),
                                $positions
                            ),
                            'data' => array_map(
                                fn (UserPosition $position): int => $position->userMessagesCount,
                                $positions
                            ),
                        ]
                    )
                );
                $image->setOptions([
                    'width' => 800,
                    'height' => 680,
                    'zoom' => 2,
                    'format' => 'png',
                    'javascript-delay' => 50,
                    'no-stop-slow-scripts',
                ]);

                $imageContent = $image->toString();

                if (is_bool($imageContent)) {
                    throw new RuntimeException('Failed to create the image!');
                }

                return new WhoCommandResponse(
                    $positions,
                    $command->chatId,
                    $imageContent
                );
            }
        );

        $this->whoCommandResponseGenerator->process($whoCommandResponse);
    }
}
