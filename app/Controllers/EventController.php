<?php

namespace App\Controllers;

class EventController {
    public static function index() {
        $db = Flight::get('db');
        $stmt = $db->query('SELECT * FROM events');
        Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function store() {
        $data = Flight::request()->data->getData();
        $db = Flight::get('db');
        $stmt = $db->prepare('INSERT INTO events (name, date) VALUES (:name, :date)');
        $stmt->execute([':name' => $data['name'], ':date' => $data['date']]);
        Flight::json(['message' => 'Event created successfully']);
    }
}
