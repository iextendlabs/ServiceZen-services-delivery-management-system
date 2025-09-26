<?php
namespace App\Logging;

use Monolog\Handler\StreamHandler;

class NoDuplicateErrorHandler extends StreamHandler
{
    public function write(array $record): void
    {
        $logFile = $this->url;
        $logLine = $record['formatted'];
        if (file_exists($logFile)) {
            $contents = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (in_array(trim($logLine), $contents)) {
                return; // Duplicate found, do not log
            }
        }
        parent::write($record);
    }
}
