<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Local;

use GuzzleHttp\Client;
use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\RenderedPageProviderInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\RenderRequest\RenderRequest;
use Nikitades\WhoCaresBot\WebApi\Domain\RenderRequest\RenderRequestRepositoryInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;

use function Safe\preg_replace;
use function Safe\sprintf;

class LocalRenderedPageProvider implements RenderedPageProviderInterface
{
    public function __construct(
        private Client $client,
        private CacheInterface $appCache,
        private RenderRequestRepositoryInterface $renderRequestRepository,
        private int $cachePeriod
    ) {
    }

    /**
     * @param array<string> $labels
     * @param array<int> $positions
     */
    public function getRegularTopImage(array $labels, array $positions): string
    {
        $key = sprintf(
            'render_top_%s_%s',
            implode(',', $positions),
            preg_replace('#[^\d^\w]#', '', implode(',', $labels))
        );

        $imageContent = $this->appCache->get(
            $key,
            function (CacheItem $cacheItem) use ($labels, $positions, $key): string {
                $cacheItem->expiresAfter($this->cachePeriod);

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

    public function getMedianImage(array $labels, array $positions): string
    {
        $key = sprintf(
            'render_median_%s_%s',
            implode(',', $positions),
            preg_replace('#[^\d^\w]#', '', implode(',', $labels))
        );

        $imageContent = $this->appCache->get(
            $key,
            function (CacheItem $cacheItem) use ($labels, $positions, $key): string {
                $cacheItem->expiresAfter($this->cachePeriod);

                $renderRequest = new RenderRequest(
                    $key,
                    ['labels' => $labels, 'data' => $positions]
                );

                $this->renderRequestRepository->saveOrUpdate($renderRequest);

                return $this->client
                    ->get(sprintf('/render/median?renderRequest=%s', $renderRequest->getKey()), $renderRequest->getData())
                    ->getBody()
                    ->getContents();
            }
        );

        return $imageContent;
    }
}
