<?php declare(strict_types = 1);

namespace Kernolab\Service;

class Logger
{
    public const LOG_FILE         = LOG_PATH . 'log.log';
    public const FORMAT           = 'Y-m-d H:i:s';
    public const SEVERITY_ERROR   = 'ERROR';
    public const SEVERITY_WARNING = 'WARNING';
    public const SEVERITY_NOTICE  = 'NOTICE';
    
    /**
     * Logs a message
     *
     * @param string $severity
     * @param string $message
     */
    public function log(string $severity, string $message): void
    {
        $text = '';
        
        try {
            $dateTime = new \DateTime();
            $text     .= sprintf('[%s][%s] %s', $dateTime->format(self::FORMAT), $severity, $message);
        } catch (\Exception $e) {
            $text .= 'Error while acquiring datetime: ' . $e->getMessage() . PHP_EOL;
            $text .= sprintf('[Error][%s] %s', $severity, $message);
        } finally {
            $text .= PHP_EOL;
            file_put_contents(self::LOG_FILE, $text, FILE_APPEND);
        }
    }
}