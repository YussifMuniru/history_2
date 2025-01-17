<?php

namespace App\Classes;



require_once("../../vendor/autoload.php");


// import the base class
use App\Classes\BaseClass;
// import the database
use App\Config\Database;
// import the redis cache
use App\Config\RedisClient;
// import the logger service
use App\Logger\AppLogger;
use App\Logger\LogLevel;


// create a class for 5D
class ClassPk10 extends BaseClass{


     protected $lottery_id ;
     protected $game_group ;
     protected $name ;



public function __construct(object $game_type){
    $this->lottery_id = $game_type->lottery_id;
    $this->game_group    = $game_type->game_group;
    $this->name          = $game_type->name;
}


public function winning_number(Array $draw_numbers) : array{
    $results = [];
    foreach ($draw_numbers as  $value) {
        $draw_number = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
        array_push($results,[self::WINNING_PERIOD_STR=>$draw_period,self::WINNING_NUMBER_STR => implode(",",$draw_number)]); 
    }
    return $results;
 }


public function dragon_tiger_history(Array $drawNumbers) {
    $historyArray = array();
    foreach ($drawNumbers as $item) {
        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];

        // Assuming dragonTigerTiePattern is a function you have defined in PHP
        $mydata = array(
            self::WINNING_PERIOD_STR => $draw_period,
            self::WINNING_NUMBER_STR => implode(",",$draw_number),
            'onex10' =>  self::dragonTigerTiePattern(0, 9, $draw_number),
            'twox9' =>   self::dragonTigerTiePattern(1, 8, $draw_number),
            'threex8' => self::dragonTigerTiePattern(2, 7, $draw_number),
            'fourx7' =>  self::dragonTigerTiePattern(3, 6, $draw_number),
            'fivex6' =>  self::dragonTigerTiePattern(4, 5, $draw_number),
            );
        array_unshift($historyArray, $mydata);
    }
    return $historyArray;
}

public function b_s_o_e_of_first_5(Array $drawNumbers) {
    $historyArray = [];
    $pos = ["first", "second", "third", "fourth", "fifth"];
foreach ($drawNumbers as $key => $item) {
        $value = $item[self::DRAW_NUMBER_STR]; 
        $draw_period = $item[self::DRAW_PERIOD_STR];
        $first_5 = array_slice($value, 0, 5);
        $res = [self::WINNING_PERIOD_STR => $draw_period,self::WINNING_NUMBER_STR => implode(",", $value)];
        $pos_key = [];
        foreach ($first_5 as $key => $value) {
           $b_s = ($value >= 6) ? "Big" : "Small";
            $o_e = ($value % 2 === 1) ? "Odd" : "Even";
            array_push($pos_key, [$pos[$key] => $b_s . " " . $o_e]);
         }
        $res['pos'] = $pos_key;
        array_unshift($historyArray,$res);
    }   
    return $historyArray;
}


public function b_s_o_e_of_sum_of_top_two(Array $drawNumbers) {
    $historyArray = [];
    foreach ($drawNumbers as $item) {
        $value = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];
        $sum = array_sum($value);
        $b_s = ($sum >= 12) ? "B" : "S";
        $o_e = ($sum % 2 === 1) ? "O" : "E";
        array_unshift($historyArray,[self::WINNING_PERIOD_STR => $draw_period,self::WINNING_NUMBER_STR => implode(",", $value), "sum"=>$sum, "b_s" =>$b_s, "o_e" => $o_e]);
    }   
    return $historyArray;
}

public function sum_of_top_two(Array $drawNumbers) {
    $historyArray = [];
   

    foreach ($drawNumbers as $item) {
            $value       = $item['draw_number'];
            $draw_period = $item['period'];
            $sum = array_sum(array_slice($value,0,2));
            array_unshift( $historyArray, ['draw_period'=>$draw_period,"winning" => implode(",", $value), "sum"=>$sum]);
    }   
    return $historyArray;
}


public function sum_of_top_three(Array $drawNumbers) {
    $historyArray = [];
   

    foreach ($drawNumbers as  $item) {
            $value = $item[self::DRAW_NUMBER_STR];
            $draw_period = $item[self::DRAW_PERIOD_STR];
            $sum = array_sum(array_slice($value,0,3));
            array_unshift( $historyArray, [self::WINNING_PERIOD_STR=>$draw_period,self::WINNING_NUMBER_STR => implode(",", $value), "sum"=>$sum]);
    }   
    return $historyArray;
}


public function pk_10_two_sides(Array $draw_numbers) : Array{

    $historyArray = [];

    foreach ($draw_numbers as $item) {
        $value       = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];
        $sum         = $value[0] + $value[1];

        array_unshift($historyArray, [self::WINNING_PERIOD_STR=>$draw_period,self::WINNING_NUMBER_STR=>implode(",",$value),"sum"=>$sum,"b_s"=> $sum > 11 ? "B":"S","o_e" => $sum % 2 == 0 ? "E":"O"]);
    }

    return $historyArray;
}



// this will generate the history for std
public function std(array $drawNumbers):array{
     return [
    'guess_rank'               => $this->winning_number($drawNumbers), 
    'fixed_place'              => $this->winning_number($drawNumbers),
    'dragon_tiger'             => $this->dragon_tiger_history($drawNumbers),
    'pick'                     => $this->winning_number($drawNumbers),
    'b_s_o_e'                  => ['first' => $this->b_s_o_e_of_first_5($drawNumbers),'top_two' => $this->b_s_o_e_of_sum_of_top_two($drawNumbers)] ,
    'sum'                      => ['top_two' => $this->sum_of_top_two($drawNumbers),'top_three' => $this->sum_of_top_three($drawNumbers) ],
    ];  
}

// this will generate the history for two_sides
public function two_sides(array $drawNumbers):array{ 
     return [
        "rapido"                   => $this->pk_10_two_sides($drawNumbers),
        'two_sides'                => $this->pk_10_two_sides($drawNumbers),
        'fixed_place_two_sides'    => $this->pk_10_two_sides($drawNumbers),
        'sum_of_top_two_two_sides' => $this->pk_10_two_sides($drawNumbers),
        ];  
 }

// this will generate the history for fantan
public function fantan(array $drawNumbers):array{  
   $final_res = [];
    foreach($drawNumbers as $key => $data){
    $draw_number = $data[self::DRAW_NUMBER_STR];
    $draw_period = $data[self::DRAW_PERIOD_STR];
    $draw_number  = array_map('intval', $draw_number);
    $sum_of_three = array_sum(array_slice($draw_number, 0, 3));
   
    $res[self::WINNING_NUMBER_STR] = $draw_number;
    $res[self::WINNING_PERIOD_STR] = $draw_period;
   
    
    $res['sum'] = $sum_of_three;
    $res['big_small'] =   self::bigSmall($sum_of_three, 17, 27, 6, 16);
    $res['odd_even'] =    self::oddEven($sum_of_three);

    $res['only_fantan2'] = self::onlyFantan2($sum_of_three);
    $final_res[] = $res;
 }
        
 return $final_res;
 }

// this will generate the history for board_games
public function board_games(array $drawNumbers):array{
    $history_array = [];
    foreach ($drawNumbers as $item) {
         $draw_number  = $item[self::DRAW_NUMBER_STR];
         $draw_period  = $item[self::DRAW_PERIOD_STR];
         $first_slice  = array_slice($draw_number,0,5);
         $second_slice = array_slice($draw_number,4,5);
         $first_half   = array_sum($first_slice);
         $second_half  = array_sum($second_slice);
         array_push($history_array,[self::WINNING_PERIOD_STR=>$draw_period,self::WINNING_NUMBER_STR=>implode(",",$draw_number), 'first_digit'=>$draw_number[0] , "fst_lst"=> $first_half > $second_half ? "first 5" :"last 5", 'guess_one' => in_array(1,$first_slice) ? "first" :"last"]);
        }    
   return $history_array;
 }















































 
public static function onlyFantan2(int $num): array {
    $remainder = $num % 4;
    if ($remainder === 0) {
        return [
            "4 Fan",
        ];
    } else if ($remainder === 1) {
        return [
            "1 Fan",
        ];
    } else if ($remainder === 2) {
        return [
            "2 Fan",
        ];
    } else if ($remainder === 3) {
        return [
            "3 Fan",
        ];
    } else {
        return ["Invalid remainder"];
    }
}

}