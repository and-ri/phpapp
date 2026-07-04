<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log {
    private $logger;

    public function __construct($channelName = 'phpapp', $logFile = 'app.log') {
        if (!is_dir(DIR_LOG)) {
            mkdir(DIR_LOG, 0755, true);
        }

        $this->logger = new Logger($channelName);

        $this->logger->pushHandler(new StreamHandler(DIR_LOG . $logFile, Logger::DEBUG));
    }

    public function info($message, array $context = []) {
        $this->logger->info($message, $context);
    }

    public function error($message, array $context = []) {
        $this->logger->error($message, $context);
    }

    public function warning($message, array $context = []) {
        $this->logger->warning($message, $context);
    }

    public function log($level, $message, array $context = []) {
        $this->logger->log($level, $message, $context);
    }

    public function exception(\Throwable $exception) {
        $this->logger->error($exception->getMessage(), ['exception' => $exception]);
    }
}
