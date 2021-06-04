<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Activity;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\Command\GenerateActivityReport\GenerateActivityReportCommand;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomUserCommand;

class ActivityCommand extends AbstractCustomUserCommand
{
    public function execute(): ServerResponse
    {
        $this->dispatch(new GenerateActivityReportCommand(
            chatId: $this->getMessage()->getChat()->getId(),
            userId: $this->getMessage()->getFrom()->getId(),
            withinDays: 1
        ));

        return Request::emptyResponse();
    }
}
