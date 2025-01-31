<?php

namespace App\Services;



require_once('C:/xampp/htdocs/history/vendor/autoload.php');


use App\Storage\Cache\CacheHelper;
use App\Storage\Logs\AppLogger;
use React\EventLoop\Loop;
use App\Services\Database;
use App\Services\RedisClient;




// create the engine class to manage the event loop
class Engine {


    // to store the queried game type records
    private $game_type_records ;



    public function __construct(){
        // load the db
        Database::load_db();

        // load the redis cache
        new RedisClient();

        // load the app logger
        new AppLogger();

        // Fetch all lottery ids and their respective seconds per issue from the database
        $this->game_type_records = Database::fetch_all_lottery_ids();

    }
        
    public function start(){

    // create event loop
    $loop = Loop::get();

    #create event loop
    $timer = $loop->addPeriodicTimer(1, function () {


        $time = time();
        $cache_status = [];

        echo date('s') . PHP_EOL;
        echo time() . PHP_EOL;
        $start_time = microtime(true);
        $total_time = 0;
        echo microtime(true) . PHP_EOL;

        foreach($this->game_type_records as $seconds_per_issue => $game_types){
            if($seconds_per_issue !== "lottery_ids"){
            if(($time % $seconds_per_issue) == 0){
            $total_time = $total_time + (microtime(true) -  $start_time);
            CacheHelper::cache_history($game_types);
            $cache_status[] = $seconds_per_issue;

            }
        }
        }
        foreach($cache_status as $cache_strings){
            echo "History cached for {". $cache_strings."} seconds.".PHP_EOL;
            
        }
       if(!empty($cache_status)) echo "Script Execution Time: {$total_time}".PHP_EOL;
    });

    #start the loop
    $loop->run();
    }

}



// Start the history engine
$engine = new Engine();
$engine->start();
unset($engine);