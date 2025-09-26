<?php
namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class SingleLineErrorFormatter extends LineFormatter
{
    public function format(array $record): string
    {
        $message = $record['message'];
        $context = $record['context'];
        $file = $context['file'] ?? '';
        $line = $context['line'] ?? '';

        // Try to extract file/line from exception if not present
        if ((empty($file) || empty($line)) && isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $file = $context['exception']->getFile();
            $line = $context['exception']->getLine();
        }

        return sprintf("%s | %s:%s\n", $message, $file, $line);
    }
}
