<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class GetTopMarkupController
{
    public function __construct(
        private Environment $twigEnvironment
    ) {
    }

    #[Route(path: '/markup/top', methods: ['GET', 'PUT'])]
    public function __invoke(Request $request): Response
    {
        return new Response($this->twigEnvironment->render('top.twig', [
            'labels' => ['One', 'Two', 'Three'],
            'data' => [1, 2, 3],
        ]));
    }
}
