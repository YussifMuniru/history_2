<?php

namespace App\Config;


require_once("../../vendor/autoload.php");

use PDO;
use PDOException;


use Flight;

// Load environment variables
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'../../../');
$dotenv->load();


// create a class to manipulate the db connection
class Database {

// initialize the database
public static function load_db(){

    try{
        $dsn = sprintf("mysql:host=%s;dbname=%s", $_ENV['DB_HOST'], $_ENV['DB_NAME']);
        echo $dsn;
        $db = new PDO($dsn,$_ENV['DB_USER'],$_ENV['DB_PASS']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        
        Flight::set('db',$db);
    }catch(PDOException $e){
         return "Db connection failed: ". $e->getMessage();
    }
}


public static function fetch_all_lottery_ids(): array | string {


     try{     

    // get access to the db
    $db = Flight::get('db');

    // make sure there is a connection 
    if(empty($db)) return [];
    $stmt = $db->prepare("SELECT game_group,seconds_per_issue FROM game_type GROUP BY seconds_per_issue");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
    $results['lottery_ids'] = [];
    foreach ($rows as $row){
    $stmt = $db->prepare("SELECT gt_id as lottery_id,game_group,name,lottery_model  FROM game_type WHERE seconds_per_issue = '{$row->seconds_per_issue}'");
    $stmt->execute();
    $gt_id_rows = $stmt->fetchAll();
    foreach($gt_id_rows as $gt_id_row){
        $results["{$row->seconds_per_issue}"][] = $gt_id_row;
        $results["lottery_ids"]["{$row->seconds_per_issue}"][] = $gt_id_row->lottery_id;
    }  
   }
    return $results;
    }catch(PDOException $e){
       return $e->getMessage();
    }




}



public static function fetch_draw_numbers($lottery_id){



$lottery_id = intval($lottery_id);
$db = Flight::get("db");

// Step 1: Fetch the table name from gamestable_map
$stmt = $db->prepare("SELECT draw_table FROM gamestable_map WHERE game_type = :id LIMIT 1");
$stmt->bindParam(":id", $lottery_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    return [
        'type' => 'error', 
        'message' => 'Invalid lottery ID', 
        'data' => []
    ];
}

$tableName = $row['draw_table'];

// Step 2: Dynamically construct and execute a query to fetch data from the determined table
$query = "SELECT draw_number, period FROM {$tableName} 
          WHERE draw_number REGEXP '^\[\"[0-9]+\"(,\"[0-9]+\")*\]$' 
          ORDER BY period DESC LIMIT :limit"; 
$stmt = $db->prepare($query);
$limit = 30;
$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process results
$response = [
    'type' => 'success',
    'message' => 'Recent Lottery Issue List',
    'data' => []
];

foreach ($results as $item) {
    $response['data'][] = [
        'draw_number' => json_decode($item['draw_number']), 
        'period' => substr($item['period'], -4) 
    ];
}

return $response;
}






































































































































































// close the db connection
public static function db_close():void {
        Flight::clear("db");    
}  


}
