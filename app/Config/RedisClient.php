<?php

namespace App\Config;


require_once("../../vendor/autoload.php");

use Predis\Client;

class RedisClient {

    private $client = null;

    public function __construct(){
         if(is_null($this->client)){
            $this->client = new Client();
        }
    }
    private static $latest_period_flag = "lp_side_history";


    public function store(string $key, string $value): mixed{
          return $this->client->set($key,$value);
    }

    public function fetch(string $key, bool $parse = true): array{
        $results = self::$client->get($key);
        return $parse ? self::parseCacheResults($results) : $results;
    }

    public function parseCacheResults(string $results, bool $as_array = true):array{

        return  json_decode($results, $as_array);

    }


    public function updateLatestDrawPeriod($lottery_id,$draw_period){
        $this->store(self::$latest_period_flag."_".$lottery_id,$draw_period);
    }
    
    public function updated_storage_history($game_types = [],$current_lottery_id = null){

        $histories_array = [];
        $current_history = [];
        foreach($game_types as $game_type){
            
            $history_function_name = "generate_history_{$game_type->game_group}";
            if(!function_exists($history_function_name)) continue;
            $generated_history_array  =  $history_function_name($game_type->lottery_id, $game_type->lottery_model);
            if (isset($generated_history_array['status'])) continue;
           
            if($game_type->lottery_id == $current_lottery_id) $current_history = $generated_history_array; 
            foreach ($generated_history_array as $history_type => $generated_history) {
                 
                  $histories_array[$history_type . "_" . $game_type->lottery_id] = $generated_history;
        }
    }
   // print_r($histories_array);
    return ['history' => cache_history_bulk($histories_array),'current_history' => $current_history];
}

}

