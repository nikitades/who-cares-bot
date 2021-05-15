<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Symfony;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionSubscriber
{
    public function onKernelException(ExceptionEvent $event)
    {
        if ($exception = $event->getThrowable() instanceof HttpExceptionInterface) {
            echo 123;
        }
    }
}
