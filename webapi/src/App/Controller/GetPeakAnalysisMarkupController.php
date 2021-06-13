<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\Controller;

use Exception;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\RenderRequest\RenderRequestRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class GetPeakAnalysisMarkupController
{
    public function __construct(
        private Environment $twigEnvironment,
        private RenderRequestRepositoryInterface $renderRequestRepository,
        private LoggerInterface $logger
    ) {
    }

    #[Route(path: '/markup/peakAnalysis/{renderRequestId}', methods: ['GET'])]
    public function __invoke(string $renderRequestId): Response
    {
        $renderRequest = $this->renderRequestRepository->findById($renderRequestId);

        if (null === $renderRequest) {
            $this->logger->error('No render request found by key ' . $renderRequestId);
            throw new Exception('No render request found by key ' . $renderRequestId);
        }

        return new Response($this->twigEnvironment->render('peakanalysis.twig', $renderRequest->getData()));
    }
}
