<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Processor\UidProcessor;

// Reference : https://www.zu-min.com/archives/567
class CustomizeFormatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        $uidProcessor = new UidProcessor();
        $formatter = new LineFormatter("[%datetime%] %channel%.%level_name% %extra.uid%: %message% %context%\n", 'Y-m-d H:i:s', true, true);
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor($uidProcessor);
            $handler->setFormatter($formatter);
        }
    }
}
