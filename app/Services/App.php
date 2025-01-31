<?php

namespace App\Services;

require_once("c:/xampp/htdocs/history/vendor/autoload.php");


use App\Services\Database;
use App\Services\RedisClient;
use App\Storage\Logs\AppLogger;



class App {

    public static function init(array $services = []){
        // load the db
       if(in_array("db", $services)) Database ::load_db();

        // load the redis cache
      if(in_array("redis", $services))   new RedisClient();

        // load the app logger
       if(in_array("logger", $services)) new AppLogger();
    }
}