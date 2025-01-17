<?php

namespace App\Storage\Cache ;

use App\Config\Database;
use App\Config\RedisClient;
use App\Logger\AppLogger;
use App\Logger\LogLevel;

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
    
public static function cache_history($game_types = []):array
{
        $history_data = [];
        $history_obj = "";
        try{
           foreach($game_types as $game_type){
                if(trim($game_type->game_group) === "pc28") continue;
               $history_class = "App\Classes\\Class".strtoupper($game_type->game_group);
               if(class_exists($history_class) && method_exists($history_class,"generate_and_store")){

                $history_obj = new $history_class($game_type);
                // Generate and store the history data   
               $history_data = $history_obj->generate_and_store();
                }else{
                 AppLogger ::customError(LogLevel ::ERROR,1900, "No class or Method found for game group: {$game_type->game_group}");
                continue;
               }
        }
        return $history_data;
    }catch(\Exception $e){
      AppLogger::error(LogLevel::ERROR, $e);
      return ["error" => "Internal Server Error."];
    }finally{
       unset($history_obj);
    }
}


public static function cache_history_bulk(array $history_data): array
{

    try {
       
        foreach ($history_data as $history_key => $history_data_array) {
            $redisClient = new RedisClient ();
            $redisClient->store($history_key, json_encode($history_data_array));
        }
        return ['status' => true, 'msg' => "success"];
    } catch (\Exception $e) {
       AppLogger::error(LogLevel::ERROR,$e);
        return ['status' => false, 'msg' => "error"];
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

        if ($history_draw_period != $server_draw_period) {
            $game_types_records          = Database::fetch_all_lottery_ids();
           
            foreach($game_types_records as $seconds_per_issue => $game_types){
                if (in_array($lottery_id, $game_types)) {
                     $history_data = self::cache_history($game_types_records[$seconds_per_issue]);
                     if(!isset($history_data["error"])) $redisClient->updateLatestDrawPeriod($lottery_id,$history_draw_period);
                     break;
                    }
                }
            }
        return json_encode($cached_history);
    } catch (\Exception $e) {
       AppLogger::error(LogLevel::ERROR, $e);
       return json_encode(["error" => "Internal Server Error."]);
    }
}

}
