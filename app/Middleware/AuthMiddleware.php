<?php

namespace App\Middleware;

require_once("C:\\xampp\\htdocs\\history\\vendor\\autoload.php");

// Load environment variables
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable("C:\\xampp\\htdocs\\history");
$dotenv->load();


use Flight;

class AuthMiddleware {
    public static function handle() {
        $headers = Flight::request()->headers();
        $token = $headers['Authorization'] ?? null;
      
        if ((!$token || $token !== $_ENV['API_SECRET_KEY']) && false) {
            Flight::halt(401, json_encode(['error' => "Unauthorized request.$token"]));
        }
    }
}
