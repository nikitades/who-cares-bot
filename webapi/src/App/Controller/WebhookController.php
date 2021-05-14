<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

final class WebhookController
{
    #[Route(path: '/api/webhook', methods: ['POST', 'GET'])]
    public function __invoke(): Response
    {
        return new Response('ok', Response::HTTP_OK);
    }
}
