<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Local;

use GuzzleHttp\Client;
use Nikitades\WhoCaresBot\WebApi\App\Command\RenderedPageProviderInterface;

class LocalRenderedPageProvider implements RenderedPageProviderInterface
{
    public function __construct(private Client $client)
    {
    }

    /**
     * @param array<string> $titles
     * @param array<int> $positions
     */
    public function getRegularTopImage(array $titles, array $positions): string
    {
        $response = $this->client->put('/render/top', ['titles' => $titles, 'positions' => $positions]);

        return $response->getBody()->getContents();
    }
}
