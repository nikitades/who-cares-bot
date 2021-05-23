<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Local;

use GuzzleHttp\Client;
use Nikitades\WhoCaresBot\WebApi\App\Command\RenderedPageProviderInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\RenderRequest\RenderRequest;
use Nikitades\WhoCaresBot\WebApi\Domain\RenderRequest\RenderRequestRepositoryInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;
use function Safe\sprintf;

class LocalRenderedPageProvider implements RenderedPageProviderInterface
{
    public function __construct(
        private Client $client,
        private CacheInterface $appCache,
        private RenderRequestRepositoryInterface $renderRequestRepository
    ) {
    }

    /**
     * @param array<string> $labels
     * @param array<int> $positions
     */
    public function getRegularTopImage(array $labels, array $positions): string
    {
        $key = sprintf('render_top_%s_%s', implode(',', $labels), implode(',', $positions));

        $imageContent = $this->appCache->get(
            $key,
            function (CacheItem $cacheItem) use ($labels, $positions, $key): string {
                $cacheItem->expiresAfter(60);

                $renderRequest = new RenderRequest(
                    $key,
                    ['labels' => $labels, 'data' => $positions]
                );

                $this->renderRequestRepository->saveOrUpdate($renderRequest);

                return $this->client
                    ->get(sprintf('/render/top?renderRequest=%s', $renderRequest->getKey()), $renderRequest->getData())
                    ->getBody()
                    ->getContents();
            }
        );

        return $imageContent;
    }
}
