<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\Response;

interface ResponseInterface
{
    public function process(): void;
}
