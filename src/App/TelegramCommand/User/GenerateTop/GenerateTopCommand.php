<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\User\GenerateTop;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Nikitades\WhoCaresBot\WebApi\App\TelegramCommand\AbstractCustomUserCommand;
use Nikitades\WhoCaresBot\WebApi\App\Command\GenerateTop\GenerateTopCommand as LocalGenerateTopCommand;

class GenerateTopCommand extends AbstractCustomUserCommand
{
    public function execute(): ServerResponse
    {
        $this->dispatch(new LocalGenerateTopCommand(
            1,
            $this->getMessage()->getChat()->getId()
        ));

        return Request::emptyResponse();
    }
}
