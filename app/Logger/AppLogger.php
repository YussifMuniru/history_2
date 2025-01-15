<?php

namespace App\Logger;

require_once("../../vendor/autoload.php");


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Flight;

 enum LogLevel {
    case ERROR;
    case INFO;
    case WARN;
 } 

class AppLogger {

    

    public function __construct() {
        // Create a new Monolog Logger instance
        $logger = new Logger(get_class($this));
        $streamHandler = new StreamHandler("app.log", Logger::DEBUG);
        $logger->pushHandler($streamHandler);

        // Set the logger instance in flight php
        Flight::set("logger", $logger);
    }

    public static function getCallerInfo():array{

        $trace = debug_backtrace();
        // Skip the first element which is the current function
        $caller = $trace[1];
        return [
            'file' => $caller['file'],
            'line' => $caller['line'],
        ];
    }

    public static function error(LogLevel  $log_level,\Exception $exception): void{
         $logger = Flight::get("logger");
         $callerInfo = self::getCallerInfo();
         $err_code   = $exception->getCode();
         $err_msg    = $exception->getMessage();
         $error_string = "Error: " . $err_code .": " . $err_msg.". Occured in file : {$callerInfo['file']} on line {$callerInfo['file']}";
         switch ($log_level) {
            case LogLevel::ERROR:
                $logger->error($error_string);
            case LogLevel::INFO:
                $logger->info($error_string);
            case LogLevel::WARN:
                $logger->warn($error_string);
            default: $logger->error($error_string);
         }
   }
    public static function customError(LogLevel  $log_level,int $err_code = 0,string $err_msg = ""): void{
         $logger = Flight::get("logger");
         $callerInfo = self::getCallerInfo();
         $error_string = "Error: " . $err_code .": " . $err_msg.". Occured in file : {$callerInfo['file']} on line {$callerInfo['file']}";
         switch ($log_level) {
            case LogLevel::ERROR:
                $logger->error($error_string);
            case LogLevel::INFO:
                $logger->info($error_string);
            case LogLevel::WARN:
                $logger->warn($error_string);
            default: $logger->error($error_string);
         }
   }


}