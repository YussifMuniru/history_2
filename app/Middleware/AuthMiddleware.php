<?php

namespace App\Middleware;

class AuthMiddleware {
    public static function handle() {
        $headers = Flight::request()->headers;
        $token = $headers['Authorization'] ?? null;

        if (!$token || $token !== getenv('API_SECRET')) {
            Flight::halt(401, json_encode(['error' => 'Unauthorized']));
        }
    }
}
