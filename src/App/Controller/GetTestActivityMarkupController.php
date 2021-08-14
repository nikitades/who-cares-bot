<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Controller;

use Exception;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\RenderRequest\RenderRequestRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class GetTestActivityMarkupController
{
    public function __construct(
        private Environment $twigEnvironment,
        private RenderRequestRepositoryInterface $renderRequestRepository,
        private LoggerInterface $logger
    ) {
    }

    #[Route(path: '/markup/activity-test', methods: ['GET'])]
    public function __invoke(): Response
    {
        $renderRequest = $this->renderRequestRepository->findById('render_activity_0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,2,0_220023000000010002000300040005000600070008000900100011001200130014001500160017001800190020002100');

        if (null === $renderRequest) {
            throw new Exception('No render request found by key ');
        }

        return new Response($this->twigEnvironment->render('activity.chartcss.twig', $renderRequest->getData()));
    }
}
