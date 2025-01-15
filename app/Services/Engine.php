<?php

namespace App\Services;

use App\Logger\AppLogger;
use App\Logger\LogLevel;

require_once('../../vendor/autoload.php');
// require_once('utils.php');
// require_once('db_utils.php');


// import React EventLoop
use React\EventLoop\Factory;

// import the database
use App\Config\Database;


// import the database
use App\Config\RedisClient;


// import the history classes
use App\Classes\Class5D;



// create the engine class to manage the event loop
class Engine {
    
public function start(){

// Fetch all lottery ids and their respective seconds per issue from the database
$game_types_records = Database::fetch_all_lottery_ids();

// create event loop
$loop = Factory::create();

#create event loop
$timer = $loop->addPeriodicTimer(1, function () use ($game_types_records) {


    $time = time();
    $cache_status = [];

    echo date('s') . PHP_EOL;
    echo time() . PHP_EOL;
    echo microtime(true) . PHP_EOL;

    foreach($game_types_records as $seconds_per_issue => $game_types){
        if($seconds_per_issue !== "lottery_ids"){
        if(($time % $seconds_per_issue) == 0){
        self::load_history($game_types);
        $cache_status[] = $seconds_per_issue;
        }
    }
    }
   foreach($cache_status as $cache_strings){
        echo "History cached for {". $cache_strings."} seconds.".PHP_EOL;
    }
});

#start the loop
$loop->run();
}



public static function load_history($game_types = [],$current_lottery_id = null) : array
{

        $histories_array = [];
        $current_history = [];

        try{

         foreach($game_types as $game_type){
            
            
               $class_name = "App\Classes\\Class".strtoupper($game_type->game_group);
              
               if(class_exists($class_name)){

                   $obj = new $class_name();
                   $generated_history_array = $obj->generate($game_type->lottery_id, $game_type->lottery_model);
                   
                   // if log any error that might happen during the generation
                   if (isset($generated_history_array['status'])) {
                        AppLogger::customError(LogLevel ::ERROR, 0, "Generating history for game type: {$game_type->name}. Lottery id found to be less than 1.");
                    }

                   if($game_type->lottery_id == $current_lottery_id) $current_history = $generated_history_array; 
                    foreach ($generated_history_array as $history_type => $generated_history) {
                        $histories_array[$history_type . "_" . $game_type->lottery_id] = $generated_history;
                        echo "ENTERED here". PHP_EOL;
                    }

                }else{
                echo "No class found for game group: {$game_type->game_group}". PHP_EOL;
                continue;
               }
      
        }
      
      return ['history' => self::cache_history_bulk($histories_array),'current_history' => $current_history];
    }catch(\Exception $e){
      AppLogger::error(LogLevel::ERROR, $e);
    }
  
}


public static function cache_history_bulk(array $history_data): array
{

    try {
       
        foreach ($history_data as $history_key => $history_data_array) {
            $redisClient = new RedisClient();
            $redisClient->store($history_key, json_encode($history_data_array));
        }
        return ['status' => true, 'msg' => "success"];
    } catch (\Exception $e) {
       AppLogger::error(LogLevel::ERROR,$e);
        return ['status' => false, 'msg' => "error"];
    }
}


public static function fetch_cached_history($lottery_id, $type): mixed
{

    try {
        $redis              = new \Predis\Client();
        $cache_key          = "{$type}_{$lottery_id}";
        $cached_history     = json_decode($redis->get($cache_key), true);
        $server_draw_period = json_decode($redis->get("currentDraw{$lottery_id}"), true);
        $server_draw_period = isset($server_draw_period) ? substr($server_draw_period, -4, 4) : "";
        $history_draw_period = $redis->get("lp_side_history{$lottery_id}");
        if ($history_draw_period != $server_draw_period) {
            
            $query_res = fetch_all_lottery_ids();
            $game_types_records = $query_res["lottery_ids"];
            
            foreach($game_types_records as $seconds_per_issue => $game_types){
               
                 if (in_array($lottery_id, $game_types)) {
                   
                     $cached_history = self::load_history($query_res[$seconds_per_issue],$lottery_id)['current_history'];
                     $cached_history = $cached_history[$type];
                     $redis->set("currentDraw{$lottery_id}",$history_draw_period);
                     break;
                    }
        
    }
 
        }
        if (!isset($cached_history)) return json_encode([]);
        return json_encode($cached_history);
    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage();
      
        return json_encode([]);
    }
}




}



// load the db
Database::load_db();

// load the redis cache
new RedisClient();

// load the app logger
new AppLogger();

(new Engine())->start();