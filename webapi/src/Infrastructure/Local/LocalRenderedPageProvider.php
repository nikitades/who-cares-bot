<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Local;

use GuzzleHttp\Client;
use Nikitades\WhoCaresBot\WebApi\App\RenderedPageProviderInterface;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\RenderRequest\RenderRequest;
use Nikitades\WhoCaresBot\WebApi\Domain\Entity\RenderRequest\RenderRequestRepositoryInterface;
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
                    ->get(sprintf('/markup/top/%s', $renderRequest->getKey()), $renderRequest->getData())
                    ->getBody()
                    ->getContents();
            }
        );

        return $imageContent;
    }

    public function getActivityImage(array $labels, array $positions): string
    {
        $key = sprintf(
            'render_activity_%s_%s',
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
                    ->get(sprintf('/markup/activity/%s', $renderRequest->getKey()), $renderRequest->getData())
                    ->getBody()
                    ->getContents();
            }
        );

        return $imageContent;
    }
}
