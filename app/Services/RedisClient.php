<?php

namespace App\Services;


require_once('C:/xampp/htdocs/history/vendor/autoload.php');

use Predis\Client;

class RedisClient {

    private $client = null;
    
    const LATEST_DRAW_PERIOD_STR = "lp_side_history_";
    const CURRENT_DRAW = "currentDraw";

  

    public function __construct(){
         if(is_null($this->client) || is_null(\Flight::get("redis"))){
            $this->client = new Client();
             // Make the redis client available through the app
            \Flight::set("redis", $this->client);    
        }
    }

    public function store(string $key, string $value): mixed{
          return $this->client->set($key,$value);
    }

    public function bulk_store($data):mixed{
        return $this->client->mset($data);
    }

    public function fetch(string $key, bool $decode = true): array | string{
        $results = ($this->client)->get($key);
        if(empty($results)) return $decode ? [] : "";
        return $decode ? $this->parseCacheResults($results) : $results;
    }

    public function parseCacheResults(string $results, bool $as_array = true):array{
           return  json_decode($results, $as_array);
        }


    public function updateLatestDrawPeriod($lottery_id,$draw_period){
        $this->store(self::LATEST_DRAW_PERIOD_STR.$lottery_id,$draw_period);
    }

    public function getLatestDrawPeriod($lottery_id): string{
       return $this->fetch(self::LATEST_DRAW_PERIOD_STR.$lottery_id,false);
    }

    public function getCurrentDraw($lottery_id): string{
       return $this->fetch(self::CURRENT_DRAW.$lottery_id,false);
    }
    

}

