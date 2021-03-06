<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\PeakAnalysis;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomUserCommand;
use Nikitades\WhoCaresBot\WebApi\App\Command\GeneratePeakAnalysisReport\GeneratePeakAnalysisReportCommand;

class PeakAnalysisCommand extends AbstractCustomUserCommand
{
    public function execute(): ServerResponse
    {
        $this->dispatch(new GeneratePeakAnalysisReportCommand(
            chatId: $this->getMessage()->getChat()->getId(),
            withinDays: 1
        ));

        return Request::emptyResponse();
    }
}
