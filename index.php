<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json, text/event-stream');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');


require_once("vendor/autoload.php");

use App\Middleware\AuthMiddleware;
use App\Services\App;
use App\Storage\Cache\CacheHelper;
use App\Utils\Utils;

App::init(['logger']);

// $logger = \Flight::get("logger");

// if(is_null($logger)){
//   echo "Logger is null";
// }

Flight::route('GET /api/v1/standard/@lottery_id', function($lottery_id) {
    AuthMiddleware::handle();
    echo CacheHelper::fetch_history($lottery_id,Utils::STANDARD);
});

Flight::route('GET /api/v1/twosides/@lottery_id', function($lottery_id) {
    AuthMiddleware::handle();
    echo CacheHelper::fetch_history($lottery_id,Utils::TWO_SIDES);
});

Flight::route('GET /api/v1/boardgames/@lottery_id', function($lottery_id) {
    AuthMiddleware::handle();
    echo CacheHelper::fetch_history($lottery_id,Utils::BOARD_GAMES);
});
Flight::route('GET /api/v1/fantan/@lottery_id', function($lottery_id) {
    AuthMiddleware::handle();
    echo CacheHelper::fetch_history($lottery_id,Utils::FANTAN);
});






























// Define a custom handler for invalid routes (404)
Flight::map('notFound', function() {
    // Set response code to 404
    http_response_code(404);

    // Return a JSON response or custom message
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
});
Flight::start();