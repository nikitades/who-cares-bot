<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\Who;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\GenerateWhoReport\GenerateWhoReportCommand;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomUserCommand;

class WhoMonthCommand extends AbstractCustomUserCommand
{
    public function execute(): ServerResponse
    {
        $this->dispatch(new GenerateWhoReportCommand(
            chatId: $this->getMessage()->getChat()->getId(),
            userId: $this->getMessage()->getFrom()->getId(),
            withinDays: 30
        ));

        return Request::emptyResponse();
    }
}
