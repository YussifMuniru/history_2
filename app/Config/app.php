<?php
require_once("../../vendor/autoload");

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

Flight::set('db', new PDO(
    'mysql:host=' . $_ENV('DB_HOST') . ';dbname=' . $_ENV('DB_NAME'),
    $_ENV('DB_USER'),
    $_ENV('DB_PASS')
));

Flight::set('redis', function() {
    $redis = new Redis();
    $redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
    return $redis;
});
