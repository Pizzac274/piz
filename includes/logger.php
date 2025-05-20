<?php
class Logger {
    private static $logFile = 'logs/system.log';
    
    public static function info($message) {
        self::log('INFO', $message);
    }
    
    public static function warning($message) {
        self::log('WARNING', $message);
    }
    
    public static function error($message) {
        self::log('ERROR', $message);
    }
    
    private static function log($level, $message) {
        // 確保日誌目錄存在
        if (!file_exists('logs')) {
            mkdir('logs', 0777, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = sprintf("[%s] [%s] %s\n", $timestamp, $level, $message);
        
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
}
?> 