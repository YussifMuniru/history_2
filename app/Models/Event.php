<?php
require_once('../../vendor/autoload.php');
// require_once('utils.php');
// require_once('db_utils.php');


class Engine extends BaseModel {

    

public static function start(){

$loop = React\EventLoop\Loop::get();

// Fetch all lottery ids and their respective seconds per issue from the database
$game_types_records = fetch_all_lottery_ids();

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
        updated_storage_history($game_types);
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


}