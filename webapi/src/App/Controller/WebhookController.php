<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Controller;

use Longman\TelegramBot\Exception\TelegramException;
use Nikitades\WhoCaresBot\WebApi\Infrastructure\Telegram\BusAwareTelegram;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class WebhookController extends AbstractController
{
    public function __construct(
        private BusAwareTelegram $telegram,
        private LoggerInterface $logger
    ) {
    }

    #[Route(path: '/api/webhook', methods: ['POST', 'GET'], format: 'json')]
    public function __invoke(): Response
    {
        $this->telegram->handle();

        $response = $this->telegram->getLastCommandResponse();

        if ($response->isOk()) {
            return new Response('ok', Response::HTTP_OK);
        } else {
            throw new TelegramException($response->getDescription(), $response->getErrorCode(), null);
        }
    }
}
