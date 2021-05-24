<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\PeakAnalysis;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\AsyncCommand\GeneratePeakAnalysis\GeneratePeakAnalysisCommand;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomUserCommand;

class PeakAnalysisCommand extends AbstractCustomUserCommand
{
    public function execute(): ServerResponse
    {
        $this->dispatch(new GeneratePeakAnalysisCommand(
            $this->getMessage()->getChat()->getId(),
            1
        ));

        return Request::emptyResponse();
    }
}
