<?php

Flight::route('GET /events',function() {
    App\Middleware\AuthMiddleware::handle();
    App\Controllers\EventController::index();
});

Flight::route('POST /events',  function() {
    App\Middleware\AuthMiddleware::handle();
    App\Controllers\EventController::store();
});


// Flight::route('GET /events', ['App\Controllers\EventController', 'index']);
// Flight::route('POST /events', ['App\Controllers\EventController', 'store']);
