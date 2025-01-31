<?php

namespace App\Storage\Cache ;

use App\Services\Database;
use App\Services\RedisClient;
use App\Storage\Logs\AppLogger;
use App\Storage\Logs\LogLevel;

class CacheHelper {
    public static function get($key) {
        $redis = Flight::get('redis');
        return $redis->get($key);
    }

    public static function set($key, $value, $ttl = 3600) {
        $redis = Flight::get('redis');
        $redis->setex($key, $ttl, $value);
    }



public function __construct(){
     new RedisClient();
}

public static function getRedisClient() {
     return new RedisClient(); 
}
    
public static function cache_history($game_types = []):mixed
{
       
        $history_data = [];
        $resultant_history = [];
        $history_obj = "";
        try{
            
           foreach($game_types as $game_type){
                if(trim($game_type->game_group) === "pc28") continue;
                $history_class = "App\Classes\\Class".strtoupper($game_type->game_group);
                if(class_exists($history_class) && method_exists($history_class,"generate_and_store")){
                $history_obj = new $history_class($game_type);
                // Generate and store the history data   
                $history_data = $history_obj->generate_and_store();
                    
            //    if(isset($history_data[$flag])){
            //     $resultant_history = $history_data[$flag];
            //    } 
                }else{
                    echo "Encountering an error but not logging";
                 AppLogger ::customError(LogLevel ::ERROR,1900, "No class or Method found for game group: {$game_type->game_group}");
               continue;
               }
        }
        return $resultant_history;
    }catch(\Exception $e){
      AppLogger::error(LogLevel::ERROR, $e);
      return ["error" => "Internal Server Error.$e"];
    }finally{
       
       unset($history_obj);
    }
}



public static function fetch_history($lottery_id, $type): mixed
{

    try {
        $redisClient        = self::getRedisClient();
        $cache_key          = "{$type}_{$lottery_id}";
        $cached_history     = $redisClient->fetch($cache_key,true);
        $server_draw_period = $redisClient->getCurrentDraw($lottery_id);
        $server_draw_period = isset($server_draw_period) ? substr($server_draw_period, -4, 4) : "";

        // return json_encode($cached_history);
        $history_draw_period = $redisClient->getLatestDrawPeriod($lottery_id);
        // echo "The Server draw period is: ".$server_draw_period;
        // echo "The latest history draw period is: ".$history_draw_period;
        $my = [];
        if ($history_draw_period != $server_draw_period) {
           
            $game_types_records          = Database::fetch_all_lottery_ids();
            foreach($game_types_records as $seconds_per_issue => $game_types){
                foreach($game_types as $i => $game_type){
                if ($lottery_id == $game_type->lottery_id) {
                     self::cache_history($game_types_records[$seconds_per_issue]);
                     if(!isset($cached_history["error"])) $redisClient->updateLatestDrawPeriod($lottery_id,$history_draw_period);
                     break;
                    }
                }
                }
                 $cached_history = $redisClient->fetch($cache_key,true);
            }
        return json_encode($cached_history);
    } catch (\Exception $e) {
       AppLogger::error(LogLevel::ERROR, $e);
       return json_encode(["error" => "Internal Server Error.$e"]);
    }
}

}


// 1. Connected chain of narration
// 2. TrustWorthy narrators
// 3. All narrators are precise in their narrations.
// 4. Absence of contradiction
// 5. Absence of hidden defects