<?php

namespace App\Logger;

require_once('C:/xampp/htdocs/history/vendor/autoload.php');


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Flight;

 enum LogLevel {
    case ERROR;
    case INFO;
    case WARN;
 } 


// Set default timezone to Ghana
date_default_timezone_set("Africa/Accra");

class AppLogger {

    public function __construct() {
        // Create a new Monolog Logger instance
        $logger = new Logger(get_class($this));
        $streamHandler = new StreamHandler("../Logger/app.log", Logger::DEBUG);
        $logger->pushHandler($streamHandler);

        // Set the logger instance in Flight PHP
        Flight::set("logger", $logger);
    }

    public static function logMessage(LogLevel $log_level, string $message): void {
        $logger = Flight::get("logger");
        switch ($log_level) {
            case LogLevel::ERROR:
                $logger->error($message);
                break;
            case LogLevel::INFO:
                $logger->info($message);
                break;
            case LogLevel::WARNING: // Assuming `LogLevel::WARN` should be `LogLevel::WARNING`
                $logger->warning($message);
                break;
            default:
                $logger->error($message);
                break;
        }
    }

    public static function error(LogLevel $log_level, \Exception $exception): void {
        $err_code = $exception->getCode();
        $err_msg = $exception->getMessage();
        $currentDateTime = date("d-m-Y H:i:s");
        $error_string = "ScriptError: {$err_code}: {$err_msg}. | ({$currentDateTime})";
        self::logMessage($log_level, $error_string);
    }

    public static function customError(LogLevel $log_level, int $err_code = 0, string $err_msg = ""): void {
        $currentDateTime = date("d-m-Y H:i:s");
        $error_string = "CustomError: {$err_code}: {$err_msg}. | ({$currentDateTime})" ;
        self::logMessage($log_level, $error_string);
    }
}
