<?php

namespace App\Helpers;

class CacheHelper {
    public static function get($key) {
        $redis = Flight::get('redis');
        return $redis->get($key);
    }

    public static function set($key, $value, $ttl = 3600) {
        $redis = Flight::get('redis');
        $redis->setex($key, $ttl, $value);
    }
}
