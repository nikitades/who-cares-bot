<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

final class TestController
{
    #[Route(path: '/api/test', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response('Okay!');
    }
}
